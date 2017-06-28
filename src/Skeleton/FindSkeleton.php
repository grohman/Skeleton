<?php
namespace Skeleton;


use Skeleton\Base\ISkeletonInit;


class FindSkeleton
{
	private const SEPARATOR            = '\\';
	private const SKELETON_CONFIG_NAME = 'SkeletonInit';
	
	
	use \Objection\TStaticClass;
	
	
	public static function getSkeleton(string $key): ?Skeleton
	{
		/** @var ISkeletonInit $instance */
		$instance = strtok($key, self::SEPARATOR) . self::SEPARATOR . self::SKELETON_CONFIG_NAME;
		
		if (class_exists($instance) && in_array(ISkeletonInit::class, class_implements($instance)))
		{
			/** @var Skeleton $result */
			$result = $instance::skeleton();
			
			return $result;
		}
		
		return null;
	}
}