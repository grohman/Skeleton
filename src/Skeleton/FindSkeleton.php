<?php
namespace Skeleton;


use Skeleton\Base\ISkeletonInit;
use Traitor\TStaticClass;


class FindSkeleton
{
	use TStaticClass;
	
	
	private const SEPARATOR            = '\\';
	private const SKELETON_CONFIG_NAME = 'SkeletonInit';
	
	
	public static function getSkeleton(string $key): ?Skeleton
	{
		$part = strtok($key, self::SEPARATOR);
		$path = $part;
		
		/** @var ISkeletonInit $className */
		$className = $path . self::SEPARATOR . self::SKELETON_CONFIG_NAME;
		
		while ($part != false)
		{
			if (class_exists($className) && in_array(ISkeletonInit::class, class_implements($className)))
			{
				/** @var Skeleton $result */
				$result = $className::skeleton();
				
				return $result;
			}
			
			$part = strtok(self::SEPARATOR);
			$path = $path . self::SEPARATOR . $part;
			$className = $path . self::SEPARATOR . self::SKELETON_CONFIG_NAME; 
		}
		
		return null;
	}
}