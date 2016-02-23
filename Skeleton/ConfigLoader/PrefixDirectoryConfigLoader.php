<?php
namespace Skeleton\ConfigLoader;


use \Skeleton\Base\IConfigLoader;
use \Skeleton\Exceptions\SkeletonException;


class PrefixDirectoryConfigLoader implements IConfigLoader
{
	/** @var array */
	private $m_aDirByPrefix = [];
	
	
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
			$this->m_aDirByPrefix[$prefix] = $directory;
		}
		else if (is_array($prefix))
		{
			$this->m_aDirByPrefix = array_merge(
				$this->m_aDirByPrefix,
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
		foreach ($this->m_aDirByPrefix as $prefix => $directory)
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