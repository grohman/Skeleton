<?php
namespace Skeleton\Base;


use Skeleton\Type;


interface IBoneConstructor
{
	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int $flags
	 * @return static
	 */
	public function set($key, $value, $flags = Type::Instance);
}