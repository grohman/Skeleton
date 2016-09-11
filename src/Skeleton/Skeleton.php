<?php
namespace Skeleton;


use Skeleton\Exceptions;
use Skeleton\Maps\SimpleMap;
use Skeleton\Base\IMap;
use Skeleton\Base\ConfigSearch;
use Skeleton\Base\IConfigLoader;
use Skeleton\Base\ISkeletonSource;


class Skeleton implements ISkeletonSource
{
	/** @var IMap */
	private $map;
	
	/** @var IConfigLoader|null */
	private $configLoader;
	
	
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
	 * @param string $key
	 * @return object|string
	 */
	public function get($key)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		if ($this->map->has($key)) 
			return $this->map->get($key);
		
		if (is_null($this->configLoader)) 
			throw new Exceptions\ImplementerNotDefinedException($key);
		
		ConfigSearch::searchFor($key, $this->map, $this->configLoader);
		
		return $this->map->get($key);
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
		else if (!is_string($value) && !is_object($value))
			throw new Exceptions\InvalidImplementerException($value);
		
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