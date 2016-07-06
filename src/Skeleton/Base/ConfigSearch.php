<?php
namespace Skeleton\Base;


class ConfigSearch
{
	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}
	
	
	/** @var string */
	private static $GLOBAL_CONFIG_PATH = ['Global'];
	
	
	/**
	 * @param $key
	 * @return array
	 */
	private static function getPathForKey($key) 
	{
		$keyPath = explode('\\', $key);
		$length = count($keyPath);
		
		if ($length == 1) $keyPath = self::$GLOBAL_CONFIG_PATH;
		else array_pop($keyPath);
		
		return $keyPath;
	}
	
	
	/**
	 * @param string $key
	 * @param IMap $map
	 * @param IConfigLoader $loader
	 */
	public static function searchFor($key, IMap $map, IConfigLoader $loader) 
	{
		$keyPath = self::getPathForKey($key);
		$length = count($keyPath);
		
		while ($length-- > 0) 
		{
			$path = implode(DIRECTORY_SEPARATOR, $keyPath);
			
			if ($loader->tryLoad($path) && $map->has($key)) break;
			
			array_pop($keyPath);
		}
	}
}