<?php
namespace Skeleton;


use \Skeleton\Exceptions;
use \Skeleton\Base\IMap;
use \Skeleton\Base\IConfigLoader;


class Skeleton
{
	/** @var IMap */
	private $m_map;
	
	/** @var IConfigLoader */
	private $m_configLoader;
	
	
	/**
	 * @param string $key
	 */
	private function tryLoad($key) 
	{
		$keyPath = explode('\\', $key);
		$length = count($keyPath);
		
		while ($length > 0) 
		{
			if ($this->m_configLoader->tryLoad(implode(DIRECTORY_SEPARATOR, $keyPath))) 
			{
				if ($this->m_map->has($key)) 
					break;
			}
			
			array_pop($keyPath);
			$length--;
		}
	}
	
	
	/**
	 * @param IMap $map
	 * @return static
	 */
	public function setMap(IMap $map) 
	{
		$this->m_map = $map;
		return $this;
	}
	
	/**
	 * @return IMap
	 */
	public function getMap()
	{
		return $this->m_map;
	}
	
	/**
	 * @param IConfigLoader $loader
	 * @return static
	 */
	public function setConfigLoader(IConfigLoader $loader)
	{
		$this->m_configLoader = $loader;
		return $this;
	}
	
	/**
	 * @return IConfigLoader
	 */
	public function getConfigLoader()
	{
		return $this->m_configLoader;
	}
	
	
	/**
	 * @param string $key
	 * @return object|string
	 */
	public function get($key)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		if ($this->m_map->has($key)) 
			return $this->m_map->get($key);
		
		$this->tryLoad($key);
		
		return $this->m_map->get($key);
	}
	
	/**
	 * @param string $key
	 * @param string|object $implementer
	 * @param int $flags
	 * @return static
	 */
	public function set($key, $implementer, $flags = Type::Instance)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		else if (!is_string($implementer) && !is_object($implementer))
			throw new Exceptions\InvalidImplementerException($implementer);
		
		$this->m_map->set($key, $implementer, $flags);
		
		return $this;
	}
}