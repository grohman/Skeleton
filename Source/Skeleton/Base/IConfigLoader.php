<?php
namespace Skeleton\Base;


interface IConfigLoader
{
	/**
	 * @param string $path
	 * @return bool
	 */
	public function tryLoad($path);

	/**
	 * @param IBoneConstructor $constructor
	 * @return static
	 */
	public function setBoneConstructor(IBoneConstructor $constructor);
}