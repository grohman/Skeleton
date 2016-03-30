<?php
namespace Skeleton;


use \Skeleton\Exceptions;
use \Skeleton\Base\IMap;
use \Skeleton\Base\ConfigSearch;
use \Skeleton\Base\IConfigLoader;


class Skeleton
{
	/** @var IMap */
	private $m_map;
	
	/** @var IConfigLoader|null */
	private $m_configLoader;
	
	
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
	 * @param IConfigLoader|null $loader
	 * @return static
	 */
	public function setConfigLoader(IConfigLoader $loader = null)
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
		
		if (is_null($this->m_configLoader)) 
			throw new Exceptions\ImplementerNotDefinedException($key);
		
		ConfigSearch::searchFor($key, $this->m_map, $this->m_configLoader);
		
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