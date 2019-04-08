<?php
namespace Skeleton\Base;


use Skeleton\Skeleton;


interface ISkeletonInit
{
	/**
	 * @param string|null $interface
	 * @return mixed|Skeleton
	 */
	public static function skeleton(?string $interface = null);
}