<?php
namespace Skeleton\ConfigLoader;


use Skeleton\Type;
use Skeleton\Base\IConfigLoader;
use Skeleton\Base\IBoneConstructor;


abstract class AbstractConfigLoader implements IConfigLoader, IBoneConstructor
{
	/** @var IBoneConstructor */
	private $constructor;
	
	
	/**
	 * @param string $directory
	 * @param string $path
	 * @return string
	 */
	protected function createPath($directory, $path)
	{
		return $directory . DIRECTORY_SEPARATOR . "$path.php";
	}
	
	/**
	 * @param string $fullPath
	 * @return bool
	 */
	protected function tryLoadSingleFile($fullPath)
	{
		if (!is_readable($fullPath)) return false;

		/** @noinspection PhpIncludeInspection */
		require_once $fullPath;
		
		return true;
	}
	
	
	/**
	 * @param IBoneConstructor $constructor
	 * @return static
	 */
	public function setBoneConstructor(IBoneConstructor $constructor)
	{
		$this->constructor = $constructor;
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int $flags
	 * @return IBoneConstructor
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		$this->constructor->set($key, $value, $flags);
		return $this->constructor;
	}
}