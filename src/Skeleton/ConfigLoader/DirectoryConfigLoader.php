<?php
namespace Skeleton\ConfigLoader;


use Skeleton\Base\IConfigLoader;


class DirectoryConfigLoader implements IConfigLoader
{
	/** @var array */
	private $directories;
	
	
	/**
	 * @param string $directory
	 * @param string $path
	 * @return string
	 */
	private function createPath($directory, $path)
	{
		return $directory . DIRECTORY_SEPARATOR . "$path.php";
	}
	
	/**
	 * @param string $fullPath
	 * @return bool
	 */
	private function tryLoadSingleFile($fullPath)
	{
		if (!is_readable($fullPath)) return false; 
		
		require_once $fullPath;
		
		return true;
	}
	
	
	/**
	 * @param array|string $directories
	 */
	public function __construct($directories) 
	{
		if (!is_array($directories))
			$directories = [$directories];
		
		$this->directories = $directories;
	}
	
	
	/**
	 * This function will try to include files in all directories passed 
	 * to the constructor, and return false if at least one directory had a config file. 
	 * @param string $path
	 * @return bool
	 */
	public function tryLoad($path)
	{
		$result = false;
		
		foreach ($this->directories as $directory)
		{
			$fullPath = $this->createPath($directory, $path); 
			$fileResult = $this->tryLoadSingleFile($fullPath);
			
			$result = $result || $fileResult;
		}
		
		return $result;
	}
}