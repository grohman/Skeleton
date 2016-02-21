<?php
namespace Skeleton;


use \Skeleton\Exceptions;
use \Skeleton\Base\IMap;
use \Skeleton\Base\IConfigLoader;


class Skeleton
{	
	/** @var IMap */
	private static $m_map;
	
	/** @var IConfigLoader */
	private static $m_configLoader;
	
	
	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}
	
	
	/**
	 * @return IMap
	 */
	public static function getMap() {
		return self::$m_map;
	}
	
	/**
	 * @param IMap $map
	 */
	public static function setMap(IMap $map) {
		self::$m_map = $map;
	}
	
	/**
	 * @return IConfigLoader
	 */
	public static function getConfigLoader()
	{
		return self::$m_configLoader;
	}
	
	/**
	 * @param IConfigLoader $loader
	 */
	public static function setConfigLoader(IConfigLoader $loader)
	{
		self::$m_configLoader = $loader;
	}
	
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public static function get($key)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		// Test first.
		// self::$m_map->get($key)
	}
	
	/**
	 * @param string $key
	 * @param string|object $implementer
	 * @param int $flags
	 */
	public static function set($key, $implementer, $flags = Type::Instance)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		else if (!is_string($implementer) && !is_object($implementer))
			throw new Exceptions\InvalidImplementerException($implementer);
		
		self::$m_map->set($key, $implementer, $flags);
	}
}