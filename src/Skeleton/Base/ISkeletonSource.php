<?php
namespace Skeleton\Base;


use Skeleton\Context;


interface ISkeletonSource
{
	/**
	 * @param string $key
	 * @param IContextReference|Context|array|null $context
	 * @param bool $skipGlobal
	 * @return mixed
	 */
	public function get($key, $context = null, bool $skipGlobal = false);

	/**
	 * @param string|mixed $item
	 * @param IContextReference|Context|array|null $context
	 * @return mixed
	 */
	public function load($item, $context = null);
}