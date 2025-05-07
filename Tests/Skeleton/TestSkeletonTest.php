<?php
namespace Skeleton;


class TestSkeletonTest extends \SkeletonTestCase
{
	public function tearDown()
	{
		parent::tearDown();
		TestSkeleton::unset();
	}
	
	
	public function test_has_KeyNotFound_ReturnFalse()
	{
		self::assertFalse(TestSkeleton::has('a'));
	}
	
	public function test_has_KeyFound_ReturnTrue()
	{
		TestSkeleton::overrideValue('a', 'b');
		self::assertTrue(TestSkeleton::has('a'));
	}
	
	
	public function test_override_ValueAdded()
	{
		TestSkeleton::override('a', $this);
		self::assertTrue(TestSkeleton::has('a'));
	}
	
	public function test_overrideValue_ValueAdded()
	{
		TestSkeleton::overrideValue('a', $this);
		self::assertTrue(TestSkeleton::has('a'));
	}
	
	
	public function test_reset_ValuesReset()
	{
		TestSkeleton::overrideValue('a', 'b');
		TestSkeleton::reset();
		
		
		self::assertFalse(TestSkeleton::has('a'));
	}
	
	
	public function test_get_ValueNotExists_ReturnNull()
	{
		self::assertNull(TestSkeleton::get('a'));
	}
	
	public function test_get_ValueSetAsValue_ValueReturned()
	{
		$f = function () { return 123; };
		
		TestSkeleton::overrideValue('a', self::class);
		TestSkeleton::overrideValue('b', $f);
		
		self::assertEquals(self::class, TestSkeleton::get('a'));
		self::assertSame($f, TestSkeleton::get('b'));
	}
	
	public function test_get_ValueSetAsCallback_CallbackInvokedAndValueUsed()
	{
		$f = function () { return 123; };
		TestSkeleton::override('a', $f);
		
		self::assertEquals(123, TestSkeleton::get('a'));
	}
	
	public function test_get_ValueSetAsClassName_NewInstanceReturned()
	{
		$c = new class {};
		
		TestSkeleton::override('a', get_class($c));
		
		self::assertInstanceOf(get_class($c), TestSkeleton::get('a'));
		self::assertNotSame($c, TestSkeleton::get('a'));
	}
	
	public function test_get_ValueSetAsInstance_InstanceReturned()
	{
		TestSkeleton::override('a', $this);
		self::assertSame($this, TestSkeleton::get('a'));
	}
	
	
	public static function test_override_SkeletonsWillObtainTheValue()
	{
		$s = new Skeleton();
		$s->setValue('a', 'b');
		
		$c = new class {};
		
		
		TestSkeleton::override('a', get_class($c));
		
		
		self::assertInstanceOf(get_class($c), $s->get('a'));
	}
	
	public static function test_overrideValue_SkeletonsWillObtainTheValue()
	{
		$s = new Skeleton();
		$s->setValue('a', 'b');
		
		
		TestSkeleton::overrideValue('a', 'c');
		
		
		self::assertEquals('c', $s->get('a'));
	}
	
	
	public static function test_override_OverrideSameValue_NewValueUsed()
	{
		$c = new class{};
		$b = new class{};
		
		TestSkeleton::override('a', get_class($b));
		TestSkeleton::override('a', get_class($c));
		
		
		self::assertInstanceOf(get_class($c), TestSkeleton::get('a'));
		self::assertNotInstanceOf(get_class($b), TestSkeleton::get('a'));
	}
	
	public static function test_overrideValue_OverrideSameValue_NewValueUsed()
	{
		TestSkeleton::overrideValue('a', 'b');
		TestSkeleton::overrideValue('a', 'c');
		
		
		self::assertEquals('c', TestSkeleton::get('a'));
	}
	
	
	public function test_unset_SkeletonObjectWillReturnOriginalValue()
	{
		$s = new Skeleton();
		
		$s->setValue('a', 'b');
		TestSkeleton::overrideValue('a', 'c');
		
		
		TestSkeleton::unset();
		
		
		self::assertEquals('b', $s->get('a'));
	}
	
	public function test_unset_OverriddenValuesReset()
	{
		TestSkeleton::overrideValue('a', 'b');
		TestSkeleton::unset();
		
		
		self::assertFalse(TestSkeleton::has('a'));
	}
}