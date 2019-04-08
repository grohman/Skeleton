<?php
namespace Skeleton\Base;


use Skeleton\Type;


interface IMap
{
	/**
	 * @return ILoader
	 */
	public function loader();
	
	/**
	 * @param ILoader $loader
	 */
	public function setLoader(ILoader $loader);
	
	/**
	 * @param ISkeletonSource $skeletonSource
	 */
	public function enableKnot(ISkeletonSource $skeletonSource);
	
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance);
	
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function forceSet($key, $value, $flags = Type::Instance);
	
	/**
	 * @param string $key
	 * @param IContextReference|null $context
	 * @return string|object
	 */
	public function get(string $key, ?IContextReference $context = null);
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key);
}