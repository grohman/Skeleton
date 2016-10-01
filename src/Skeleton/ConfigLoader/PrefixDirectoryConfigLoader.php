<?php
namespace Skeleton\ConfigLoader;


use Skeleton\Base\IConfigLoader;
use Skeleton\Exceptions\SkeletonException;


class PrefixDirectoryConfigLoader extends AbstractConfigLoader  implements IConfigLoader
{
	/** @var array */
	private $dirByPrefix = [];
	
	
	/**
	 * @param string $needle
	 * @param string $value
	 * @return bool
	 */
	private function isStartsWith($needle, $value)
	{
		$length = strlen($needle);
		
		return (
			strlen($value) >= $length && 
			substr($value, 0, $length) == $needle);
	}
	
	
	/**
	 * @param string|array $prefix
	 * @param bool $directory
	 */
	public function __construct($prefix, $directory = false)
	{
		$this->add($prefix, $directory);
	}
	
	
	/**
	 * @param string|array $prefix
	 * @param bool $directory
	 */
	public function add($prefix, $directory = false) 
	{
		if (!is_string($prefix) && !is_array($prefix))
			throw new SkeletonException('Unexpected parameter value for $prefix. Value must be string or array!');
		
		if (is_string($prefix) && $directory)
		{
			$this->dirByPrefix[$prefix] = $directory;
		}
		else if (is_array($prefix))
		{
			$this->dirByPrefix = array_merge(
				$this->dirByPrefix,
				$prefix
			);
		}
	}
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function tryLoad($path) 
	{
		foreach ($this->dirByPrefix as $prefix => $directory)
		{
			if (!$this->isStartsWith($prefix, $path))
				continue;
			
			$fullPath = $this->createPath($directory, $path); 
			
			if ($this->tryLoadSingleFile($fullPath)) 
				return true;
		}
		
		return false;
	}
}