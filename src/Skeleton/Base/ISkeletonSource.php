<?php
namespace Skeleton\Base;


interface ISkeletonSource
{
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key);
}