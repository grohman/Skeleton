<?php
namespace FindSkeletonTestB\Hello\World;


use Skeleton\Skeleton;
use Skeleton\Base\ISkeletonInit;


class SkeletonInit implements ISkeletonInit
{
	public static $skeleton;
	
	/**
	 * @param string|null $interface
	 * @return mixed|Skeleton
	 */
	public static function skeleton(?string $interface = null)
	{
		return self::$skeleton;
	}
}


SkeletonInit::$skeleton = new Skeleton();