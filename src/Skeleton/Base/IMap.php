<?php
namespace Skeleton\Base;


use \Skeleton\Type;


interface IMap 
{
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance);
	
	/**
	 * @param string $key
	 * @return string|object
	 */
	public function get($key);
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key);
}