<?php
namespace Skeleton;


use Skeleton\Exceptions\SkeletonException;


/**
 * @method static Skeleton skeleton()
 * @method static array getSubModules()
 * @method static array getComponent()
 */
trait TStaticModule
{
	use \Objection\TStaticClass;
	
	
	/** @var static[] */
	private static $subModules = null;
	
	/** @var array */
	private static $components = null;
	
	
	private static function initialize() 
	{
		self::$components = self::getComponent();
		self::$subModules = self::getSubModules();
	}
	
	
	/**
	 * @param string $name
	 * @param mixed $arguments
	 * @return mixed
	 * @throws SkeletonException
	 */
	public static function __callStatic($name, $arguments)
	{
		if (!self::$components)
		{
			self::initialize();
		}
		else if (isset(self::$components[$name]))
		{
			return self::skeleton()->get(self::$components[$name]);
		}
		// TODO: this behavior requires php 7.*
		else if (isset(self::$subModules[$name]))
		{
			return self::$subModules[$name];
		}
		
		throw new SkeletonException("Unrecognized component or sub module '$name'");
	}
}