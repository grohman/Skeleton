<?php
namespace Skeleton;


class TStaticModuleHelper
{
	use TStaticModule;
	
	
	/** @var Skeleton */
	public static $_skeleton;
	
	public static $_modules;
	public static $_component;
	
	
	public static function skeleton() { return self::$_skeleton; }
	public static function getSubModules() { return self::$_modules; }
	public static function getComponent() { return self::$_component; }
	
	
	public static function resetHelper()
	{
		TStaticModuleHelper::$_skeleton = new Skeleton();
		TStaticModuleHelper::$_modules = [];
		TStaticModuleHelper::$_component = [];
		TStaticModuleHelper::$components = null;
		TStaticModuleHelper::$subModules = null;
	}
}


class TStaticModuleTest extends \SkeletonTestCase
{
	protected function setUp()
	{
		TStaticModuleHelper::resetHelper();
	}
	
	
	/**
	 * @expectedException \Skeleton\Exceptions\SkeletonException
	 */
	public function test_CallOnUnExistingElement_ErrorThrown()
	{
		/** @noinspection PhpUndefinedMethodInspection */
		$a = TStaticModuleHelper::NotFound();
	}
	
	
	public function test_GetComponent_ComponentReturnedFromSkeleton()
	{
		TStaticModuleHelper::$_component = ['hello' => 'world'];
		TStaticModuleHelper::$_skeleton->set('world', 'correct', Type::ByValue);
		TStaticModuleHelper::$_skeleton->set('hello', 'wrong_one', Type::ByValue);
		
		
		/** @noinspection PhpUndefinedMethodInspection */
		$res = TStaticModuleHelper::hello();
		
		self::assertEquals('correct', $res);
	}
	
	public function test_GetSubModule_ComponentReturnedFromSkeleton()
	{
		TStaticModuleHelper::$_component = ['hello' => 'world'];
		TStaticModuleHelper::$_modules = ['module' => TStaticModuleHelper::class];
		
		TStaticModuleHelper::$_skeleton->set('world', 'correct', Type::ByValue);
		TStaticModuleHelper::$_skeleton->set('hello', 'wrong_one', Type::ByValue);
		
		
		/** @noinspection PhpUndefinedMethodInspection */
		$res = TStaticModuleHelper::module()::hello();
		
		self::assertEquals('correct', $res);
	}
}