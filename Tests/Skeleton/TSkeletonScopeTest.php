<?php
namespace Skeleton;


class TSkeletonScopeTest extends \SkeletonTestCase
{
	protected function setUp()
    {
    	TestObject::reset();
	}
	
	
	public function test_sanity()
	{
		$object = TestObject::skeleton();
		TestObject::reset();
		self::assertNotSame($object, TestObject::skeleton());
	}
	
	
	public function test_CalledFirstTime_SkeletonObjectReturned()
	{
		$object = TestObject::skeleton();
		self::assertInstanceOf(Skeleton::class, $object);
	}
	
	public function test_CalledAgain_SameObjectAlwaysReturned()
	{
		self::assertSame(TestObject::skeleton(), TestObject::skeleton());
	}
	
	public function test_SetupForSkeletonCalled()
	{
		$object = TestObject::skeleton();
		self::assertSame($object, TestObject::$calledWith);
	}
	
	public function test_ArgumentPassedToSkeletonObject()
	{
		self::assertEquals(1, TestObject::skeleton('a'));
	}
}


class TestObject
{
	use TSkeletonScope;

	
	public static $calledWith = null;
	
	
	private static function setupSkeleton(Skeleton $skeleton)
	{
		self::$calledWith = $skeleton;
		$skeleton->set('a', 1);
	}
	
	
	public static function reset()
	{
		self::$skeleton = null;
		self::$calledWith = null;
	}
}