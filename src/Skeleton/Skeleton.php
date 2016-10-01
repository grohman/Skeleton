<?php
namespace Skeleton;


use Skeleton\Exceptions;
use Skeleton\Maps\SimpleMap;
use Skeleton\Base\IMap;
use Skeleton\Base\ConfigSearch;
use Skeleton\Base\IConfigLoader;
use Skeleton\Base\IBoneConstructor;
use Skeleton\Base\AbstractSkeletonSource;


class Skeleton extends AbstractSkeletonSource implements IBoneConstructor
{
	/** @var bool */
	private $useGlobal = false;
	
	/** @var IMap */
	private $map;
	
	/** @var IConfigLoader|null */
	private $configLoader;
	
	
	/**
	 * @param string $key
	 * @return bool
	 */
	private function tryLoadLocal($key)
	{
		if (is_null($this->configLoader))
			return false;
		
		ConfigSearch::searchFor($key, $this->map, $this->configLoader);
		
		return $this->map->has($key);
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	private function tryLoadGlobal($key)
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
	 * @param IMap $map
	 * @return static
	 */
	public function setMap(IMap $map) 
	{
		$this->map = $map;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function useGlobal()
	{
		$this->useGlobal = true;
		return $this;
	}
	
	/**
	 * @return IMap
	 */
	public function getMap()
	{
		return $this->map;
	}
	
	/**
	 * @param IConfigLoader|null $loader
	 * @return static
	 */
	public function setConfigLoader(IConfigLoader $loader = null)
	{
		$loader->setBoneConstructor($this);
		$this->configLoader = $loader;
		return $this;
	}
	
	/**
	 * @return IConfigLoader
	 */
	public function getConfigLoader()
	{
		return $this->configLoader;
	}
	
	/**
	 * @return static
	 */
	public function enableKnot()
	{
		$this->map->enableKnot($this);
		return $this;
	}
	
	/**
	 * @param string $prefix
	 * @return $this
	 */
	public function registerGlobalFor($prefix)
	{
		GlobalSkeleton::instance()->add($prefix, $this);
		return $this;
	}
	
	
	/**
	 * @param string $key
	 * @param bool $useGlobal
	 * @return object|string
	 */
	public function get($key, $useGlobal = true)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		if ($this->map->has($key) ||
			$this->tryLoadLocal($key))
		{
			return $this->map->get($key);
		}
		else if ($useGlobal)
		{
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
		
		throw new Exceptions\ImplementerNotDefinedException($key);
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int $flags
	 * @return static
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		$this->map->set($key, $value, $flags);
		
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int $flag
	 * @return static
	 */
	public function override($key, $value, $flag = Type::Instance)
	{
		$this->map->forceSet($key, $value, $flag);
		return $this;
	}
	
	/**
	 * @param string $className
	 * @return mixed
	 */
	public function load($className)
	{
		return $this->map->loader()->get($className);
	}
}	