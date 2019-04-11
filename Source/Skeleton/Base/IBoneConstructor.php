<?php
namespace Skeleton\Base;


use Skeleton\Type;


interface IBoneConstructor
{
	/**
	 * @param string|string[] $key
	 * @param mixed $value
	 * @param int $flags
	 * @return static|IBoneConstructor
	 */
	public function set($key, $value, int $flags = Type::Instance): IBoneConstructor;
	
	public function setValue(string $key, $value): IBoneConstructor;
}