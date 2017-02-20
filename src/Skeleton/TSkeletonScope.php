<?php
namespace Skeleton;


trait TSkeletonScope
{
	use \Objection\TStaticClass;
	
	
	/** @var Skeleton */
	private static $skeleton;
	
	
	private static function setupSkeleton(Skeleton $skeleton) {}


	/**
	 * @param string $interface
	 * @return mixed|Skeleton
	 */
	public static function skeleton($interface = '')
	{
		if (is_null(self::$skeleton))
		{
			self::$skeleton = new Skeleton();
			self::setupSkeleton(self::$skeleton);
		}
		
		if (!$interface)
		{
			return self::$skeleton;
		}
		
		return self::$skeleton->get($interface);
	}
}