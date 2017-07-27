<?php
namespace Skeleton\Base;


interface ISkeletonSource
{
	/**
	 * @param string $key
	 * @param IContextReference|null $context
	 * @param bool $skipGlobal
	 * @return mixed
	 */
	public function get($key, ?IContextReference $context = null, bool $skipGlobal = false);
}