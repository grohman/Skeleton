<?php
namespace tests\Skeleton\ConfigLoader\Files;


class LoadedNotifier {

	private static $loaded = [];
	
	public static function loaded($path)
	{
		self::$loaded[] = $path;
	}
	
	public static function isLoaded($path) 
	{
		return in_array($path, self::$loaded);
	}
	
	/**
	 * At which order the file was loaded.
	 * @param string $path
	 * @param int $position 1 based
	 * @return bool
	 */
	public static function isLoadedAt($path, $position) 
	{
		$position--;
		return isset(self::$loaded[$position]) && self::$loaded[$position] === $path;
	}
	
	public static function clear() 
	{
		self::$loaded = [];
	}
}