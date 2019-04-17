<?php
namespace Skeleton;


use Skeleton\Maps\SimpleMap;


class TestSkeleton
{
	/** @var SimpleMap */
	private static $map = null;
	
	
	private static function overrideIsTestValueInSkeleton(bool $to): void
	{
		$r = new \ReflectionProperty(Skeleton::class, '_isTest');
		$r->setAccessible(true);
		$r->setValue(null, $to);
	}
	
	private static function setup(): void
	{
		if (!self::$map)
		{
			self::$map = new SimpleMap();
		}
	}
	
	
	public static function override(string $key, $value): void
	{
		self::setup();
		self::overrideIsTestValueInSkeleton(true);
		self::$map->forceSet($key, $value);
	}
	
	public static function overrideValue(string $key, $value): void
	{
		self::setup();
		self::overrideIsTestValueInSkeleton(true);
		self::$map->forceSet($key, $value, Type::ByValue);
	}
	
	public static function has(string $key): bool
	{
		self::setup();
		return self::$map->has($key);
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public static function get(string $key)
	{
		self::setup();
		return self::$map->has($key) ? self::$map->get($key) : null;
	}
	
	public static function reset(): void
	{
		self::$map = new SimpleMap();
	}
	
	public static function unset(): void
	{
		self::$map = new SimpleMap();
		self::overrideIsTestValueInSkeleton(false);
	}
}