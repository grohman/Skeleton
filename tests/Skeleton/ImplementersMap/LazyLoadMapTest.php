<?php
namespace Skeleton\ImplementersMap;


use \Skeleton\Type;
use \Skeleton\ISingleton;


class LazyLoadMapTest extends \PHPUnit_Framework_TestCase 
{
	public function test_set_FirstTime() 
	{
		$map = new LazyLoadMap();
		$map->set('a', \stdClass::class);
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\ImplementerAlreadyDefinedException
	 */
	public function test_set_KeyAlreadySet_ErrorIsThrown()
	{
		$map = new LazyLoadMap();
		$map->set('a', \stdClass::class);
		
		$map->set('a', \stdClass::class);
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_NoImplementerDefinedForKey_ErrorIsThrown()
	{
		$map = new LazyLoadMap();
		$map->get("a");
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\InvalidKeyException
	 */
	public function test_get_InvalidKey_ErrorIsThrown()
	{
		$map = new LazyLoadMap();
		$map->get(123);
	}
	
	public function test_get_ValueIsObject_ValueIsReturned()
	{
		$map = new LazyLoadMap();
		$map->set('b', new \stdClass(), Type::StaticClass);
		$this->assertEquals(new \stdClass(), $map->get('b'));
	}
	
	public function test_get_SetWithFlagStaticClass_ValueIsReturned()
	{
		$map = new LazyLoadMap();
		$map->set('a', \stdClass::class, Type::StaticClass);
		
		$this->assertEquals(\stdClass::class, $map->get('a'));
		$this->assertEquals(\stdClass::class, $map->get('a'));
	}
	
	public function test_get_SetWithFlagSingleton_SameInstanceAlwaysReturned()
	{
		$map = new LazyLoadMap();
		$map->set('a', \stdClass::class, Type::Singleton);
		
		$this->assertSame($map->get('a'), $map->get('a'));
	}
	
	public function test_get_ClassIsInstanceOfISingleton_SameInstanceAlwaysReturned()
	{
		$single = $this->getMock(ISingleton::class);
		$className = get_class($single);
		
		$map = new LazyLoadMap();
		$map->set('a', $className, Type::Instance);
		
		$this->assertSame($map->get('a'), $map->get('a'));
	}
	
	public function test_get_SetWithFlagInstance_NewInstanceAlwaysReturned()
	{
		$map = new LazyLoadMap();
		$map->set('a', \stdClass::class, Type::Instance);
		
		$this->assertNotSame($map->get('a'), $map->get('a'));
	}
	
	public function test_get_NotStaticClass_ObjectInstanceIsReturned()
	{
		$map = new LazyLoadMap();
		
		$map->set('a', \stdClass::class, Type::Instance);
		$map->set('b', \stdClass::class, Type::Singleton);
		
		$this->assertInstanceOf(\stdClass::class, $map->get('a'));
		$this->assertInstanceOf(\stdClass::class, $map->get('b'));
	}
	
	public function test_get_NumberOfKeysDefined_CorrectValueReturned()
	{
		$map = new LazyLoadMap();
		
		$a = new \stdClass();
		$b = new \stdClass();
		
		$map->set('a', $a);
		$map->set('b', $b);
		
		$this->assertNotSame($b, $map->get('a'));
		$this->assertNotSame($a, $map->get('b'));
	}
	
	public function test_get_ValueIsScalar()
	{
		$map = new LazyLoadMap();
		$map->set('a', 1);
		$this->assertEquals(1, $map->get('a'));
	}
	
	public function test_get_ValueIsArray()
	{
		$map = new LazyLoadMap();
		$map->set('a', ['a' => 'b']);
		$this->assertEquals(['a' => 'b'], $map->get('a'));
	}
	
	public function test_get_ValueIsCallback()
	{
		$map = new LazyLoadMap();
		$map->set('a', function() { return 1; });
		$this->assertEquals(1, $map->get('a'));
	}
	
	
	public function test_get_CallbackMarkedAsSingleTone_CalledOnce()
	{
		$map = new LazyLoadMap();
		$map->set('a', function() { static $i = 0; return $i++; }, Type::Singleton);
		
		$this->assertEquals(0, $map->get('a'));
		$this->assertEquals(0, $map->get('a'));
	}
	
	public function test_get_CallbackMarkedAsInstance_CalledEachTime()
	{
		$map = new LazyLoadMap();
		$map->set('a', function() { static $i = 0; return $i++; }, Type::Instance);
		
		$this->assertEquals(0, $map->get('a'));
		$this->assertEquals(1, $map->get('a'));
	}
	
	public function test_get_CallbackMarkedAsStatic_CalledOnce()
	{
		$map = new LazyLoadMap();
		$map->set('a', function() { static $i = 0; return $i++; }, Type::StaticClass);
		
		$this->assertEquals(0, $map->get('a'));
		$this->assertEquals(1, $map->get('a'));
	}
	
	
	public function test_has_KeyNotDefined_ReturnFalse()
	{
		$map = new LazyLoadMap();
		$map->set('key-1', 'value');
		
		$this->assertFalse($map->has('key'));
	}
	
	public function test_has_ValueIsClassName_ReturnTrue()
	{
		$map = new LazyLoadMap();
		$map->set('key', \stdClass::class);
		
		$this->assertTrue($map->has('key'));
	}
	
	public function test_has_ValueIsAScalarObject_ReturnTrue()
	{
		$map = new LazyLoadMap();
		$map->set('key', 'value');
		
		$this->assertTrue($map->has('key'));
	}
	
	public function test_has_ValueStillExistsAfterFirstCall()
	{
		$map = new LazyLoadMap();
		$map->set('key', \stdClass::class, Type::Singleton);
		$map->get('key');
		
		$this->assertTrue($map->has('key'));
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\InvalidKeyException
	 */
	public function test_has_KeyNotString_ErrorIsThrown()
	{
		$map = new LazyLoadMap();
		$map->has(12);
	}
	
	
	public function test_allowOverride_Called_ValueReset()
	{
		$map = new LazyLoadMap();
		$map->set('a', 'b');
		
		$map->allowOverride();
		
		$map->set('a', ['c']);
		
		$this->assertEquals(['c'], $map->get('a'));
	}
}