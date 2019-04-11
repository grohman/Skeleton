<?php
namespace Skeleton;


use Skeleton\Base\ISkeletonInit;
use Skeleton\Exceptions\StaticSkeletonPropertyMissing;


abstract class AbstractSkeleton implements ISkeletonInit
{
	/** @var Skeleton */
	protected static $skeleton = false;
	
	
	private static function setup()
	{
		static::$skeleton = static::createSkeleton();
		static::configureSkeleton(static::$skeleton);
	}
	
	
	protected static function createSkeleton(): Skeleton { return new Skeleton(); }
	protected static function configureSkeleton(Skeleton $s) {}
	
	
	/**
	 * @param string|null $interface
	 * @return mixed|Skeleton
	 */
	public static function skeleton(?string $interface = null)
	{
		if (static::$skeleton === false)
			throw new StaticSkeletonPropertyMissing();
		
		if (!static::$skeleton)
			self::setup();
		
		if ($interface)
			return static::$skeleton->get($interface);
		
		return static::$skeleton;
	}
	
	/**
	 * @param mixed $item
	 * @return mixed
	 */
	public static function load($item)
	{
		return self::skeleton()->load($item);
	}
}