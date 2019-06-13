<?php
namespace Skeleton;


use Skeleton\Maps\SimpleMap;
use Skeleton\ProcessMock\IProcessMock;
use Skeleton\ProcessMock\ProcessMock;


class TestSkeleton
{
	/** @var ProcessMock */
	private static $processMock = null;
	
	/** @var SimpleMap */
	private static $map = null;
	
	
	private static function getProcessMock(): ProcessMock
	{
		if (!self::$processMock)
		{
			$file = realpath(__DIR__ . '/../../Mock') . '/process_mock.php';
			self::$processMock = new ProcessMock($file);
		}
		
		return self::$processMock;
	}
	
	
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
	
	
	public static function includeMockFileIfExists(): bool
	{
		return self::getProcessMock()->includeIfExists();
	}
	
	public static function processMock(): IProcessMock
	{
		return self::getProcessMock();
	}
}