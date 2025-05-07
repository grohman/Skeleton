<?php
namespace Skeleton;


use Skeleton\Exceptions;
use Skeleton\Base\IMap;
use Skeleton\Maps\SimpleMap;
use Skeleton\Base\ConfigSearch;
use Skeleton\Base\IConfigLoader;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IBoneConstructor;
use Skeleton\Base\IContextReference;
use Skeleton\Tools\ContextManager;
use Skeleton\ConfigLoader\DirectoryConfigLoader;


class Skeleton implements ISkeletonSource, IBoneConstructor
{
	private static $_isTest = false;
	
	/** @var Skeleton|null */
	private static $globalContainer = null;
	
	private $useGlobal		= false;
	private $configLimit	= null;
	private $configLimitLen	= 0;
	
	/** @var IMap */
	private $map;
	
	/** @var IConfigLoader|null */
	private $configLoader;
	
	
	private function tryLoadLocal(string $key): bool
	{
		if ($this->configLimit)
		{
			if (substr($key, 0, $this->configLimitLen) != $this->configLimit)
			{
				return false;
			}
		}
		
		if (is_null($this->configLoader))
			return false;
		
		ConfigSearch::searchFor($key, $this->map, $this->configLoader);
		
		return $this->map->has($key);
	}
	
	private function tryLoadGlobal(string $key)
	{
		if ($this->useGlobal)
			return GlobalSkeleton::instance()->get($key);
		
		throw new Exceptions\ImplementerNotDefinedException($key);
	}
	
	
	public function __construct() 
	{
		$this->map = new SimpleMap();
	}
	
	
	/**
	 * @deprecated
	 * @param IMap $map
	 * @return Skeleton
	 */
	public function setMap(IMap $map): Skeleton
	{
		$this->map = $map;
		return $this;
	}
	
	/**
	 * @deprecated 
	 * @return IMap
	 */
	public function getMap(): IMap
	{
		return $this->map;
	}
	
	public function useGlobal(): Skeleton
	{
		$this->useGlobal = true;
		return $this;
	}
	
	public function setConfigLoader(IConfigLoader $loader = null, ?string $namespaceLimit = null): Skeleton
	{
		if ($loader)
		{
			$loader->setBoneConstructor($this);
			$this->configLimit = $namespaceLimit;
			$this->configLimitLen = strlen($this->configLimit ?? '');
		}
		
		$this->configLoader = $loader;
		return $this;
	}
	
	public function getConfigLoader(): ?IConfigLoader
	{
		return $this->configLoader;
	}
	
	public function enableKnot(): Skeleton
	{
		$this->map->enableKnot($this);
		return $this;
	}
	
	public function registerGlobalFor(string $prefix): Skeleton
	{
		GlobalSkeleton::instance()->add($prefix, $this);
		return $this;
	}
	
	
	/**
	 * @param string $key
	 * @param IContextReference|Context|array|null $context
	 * @param bool $skipGlobal
	 * @return mixed
	 */
	public function get($key, $context = null, bool $skipGlobal = false)
	{
		if (self::$_isTest && TestSkeleton::has($key))
			return TestSkeleton::get($key);
		
		if ($context && !$context instanceof IContextReference)
			$context = ContextManager::create($this, $context);
		
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		if ($this->map->has($key) ||
			$this->tryLoadLocal($key))
		{
			return $this->map->get($key, $context);
		}

		if ($skipGlobal)
			throw new Exceptions\ImplementerNotDefinedException($key);
		
		try 
		{
			return $this->tryLoadGlobal($key);
		}
		// Reset call stuck to be relative to this method. 
		catch (Exceptions\ImplementerNotDefinedException $e)
		{
			throw new Exceptions\ImplementerNotDefinedException($key);
		}
	}
	
	/**
	 * @param string|string[] $key
	 * @param mixed $value
	 * @param int $flags
	 * @return static|IBoneConstructor
	 */
	public function set($key, $value, int $flags = Type::Instance): IBoneConstructor
	{
		if (is_array($key))
		{
			foreach ($key as $k)
			{
				$this->set($k, $value, $flags);
			}
			
			return $this;
		}
			
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		$this->map->set($key, $value, $flags);
		
		return $this;
	}
	
	public function setValue(string $key, $value): IBoneConstructor
	{
		return $this->set($key, $value, Type::ByValue);
	}
	
	public function override(string $key, $value, int $flag = Type::Instance): Skeleton
	{
		$this->map->forceSet($key, $value, $flag);
		return $this;
	}
	
	/**
	 * @param string|mixed $item
	 * @param IContextReference|Context|array|null $context
	 * @return mixed
	 */
	public function load($item, $context = null)
	{
		if ($context && !($context instanceof IContextReference))
			$context = ContextManager::create($this, $context);
		
		return $this->map->loader()->get($item, $context);
	}
	
	public function for($instance): IContextReference
	{
		if (is_array($instance))
			return ContextManager::create($this, $instance);
		
		return ContextManager::get($instance, $this);
	}
	
	public function context($instance, ?string $name = null): Context
	{
		return ContextManager::init($instance, $this, $name);
	}
	
	
	public static function container($item)
	{
		if (!self::$globalContainer)
		{
			self::$globalContainer = new Skeleton();
			self::$globalContainer->useGlobal = true;
			self::$globalContainer->enableKnot();
		}
		
		if (is_string($item) && !class_exists($item))
		{
			return self::$globalContainer->get($item);
		}
		else
		{
			return self::$globalContainer->load($item);
		}
	}
	
	public static function create(string $namespace, ?string $directory = null): Skeleton
	{
		$skeleton = new Skeleton();
		
		if ($directory)
		{
			$skeleton->setConfigLoader(new DirectoryConfigLoader($directory), $namespace);
		}
		
		return $skeleton
			->enableKnot()
			->registerGlobalFor($namespace)
			->useGlobal();
	}
}