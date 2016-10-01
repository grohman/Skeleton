<?php
namespace Skeleton\ConfigLoader;


use Skeleton\Base\IConfigLoader;


class DirectoryConfigLoader extends AbstractConfigLoader implements IConfigLoader
{
	/** @var array */
	private $directories;
	
	
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