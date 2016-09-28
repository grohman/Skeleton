<?php
namespace Skeleton\Base;


interface ISkeletonSource
{
	/**
	 * @param string $key
	 * @param bool $useGlobal
	 * @return mixed
	 */
	public function get($key, $useGlobal = true);
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getLocal($key);
}