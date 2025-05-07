<?php
namespace Skeleton\Maps;


use Skeleton\Type;
use Skeleton\ISingleton;
use Skeleton\Base\ILoader;
use Skeleton\Loader\ValueLoader;


class SimpleMapTest extends \SkeletonTestCase 
{
	/** @var ILoader|\PHPUnit_Framework_MockObject_MockObject */
	private $loader;
	
	
	private function getSimpleMap()
	{
		$this->loader = $this->getMock(ILoader::class);
		
		$map = new SimpleMap();
		$map->setLoader($this->loader);
		
		return $map;
	}
	
	
	public function test_set_FirstTime() 
	{
		$map = $this->getSimpleMap();
		$map->set('a', \stdClass::class);
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\ImplementerAlreadyDefinedException
	 */
	public function test_set_KeyAlreadySet_ErrorIsThrown()
	{
		$map = $this->getSimpleMap();
		$map->set('a', \stdClass::class);
		
		$map->set('a', \stdClass::class);
	}
	
	
	public function test_forceSet_FirstTime()
	{
		$map = $this->getSimpleMap();
		$map->forceSet('a', \stdClass::class);
	}
	
	public function test_forceSet_KeyAlreadySet_NoErrorIsThrown()
	{
		$map = $this->getSimpleMap();
		$map->set('a', \stdClass::class);
		
		$map->forceSet('a', \stdClass::class);
	}
	
	public function test_forceSet_OverrideTypeWithValue_NewValueIsUsed()
	{
		$map = new SimpleMap();
		$map->setLoader(new ValueLoader());
		$map->set('a', \stdClass::class);
		
		$map->forceSet('a', 'B', Type::ByValue);
		
		$this->assertEquals('B', $map->get('a'));
	}
	
	public function test_forceSet_OverrideValueWithValue_NewValueIsUsed()
	{
		$map = new SimpleMap();
		$map->setLoader(new ValueLoader());
		$map->set('a', 'C', Type::ByValue);
		
		$map->forceSet('a', 'B', Type::ByValue);
		
		$this->assertEquals('B', $map->get('a'));
	}
	
	public function test_forceSet_OverrideTypeWithType_NewValueIsUsed()
	{
		$map = new SimpleMap();
		$map->setLoader(new ValueLoader());
		$map->set('a', self::class);
		
		$map->forceSet('a', \stdClass::class);
		
		$this->assertInstanceOf(\stdClass::class, $map->get('a'));
	}
	
	public function test_forceSet_OverrideValueWithType_NewValueIsUsed()
	{
		$map = new SimpleMap();
		$map->setLoader(new ValueLoader());
		$map->set('a', 'C');
		
		$map->forceSet('a', \stdClass::class);
		
		$this->assertInstanceOf(\stdClass::class, $map->get('a'));
	}
	
	
	/**
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_NoImplementerDefinedForKey_ErrorIsThrown()
	{
		$map = $this->getSimpleMap();
		$map->get("a");
	}
	
	public function test_get_ValueIsObject_ValueReturned()
	{
		$map = $this->getSimpleMap();
		
		$object = new \stdClass();
		$map->set('b', $object);
		
		$this->assertSame($object, $map->get('b'));
	}
	
	public function test_get_ValueIsArray_ValueReturned()
	{
		$map = $this->getSimpleMap();
		
		$array = ['a' => 2, 'b' => 3];
		$map->set('b', $array);
		
		$this->assertSame($array, $map->get('b'));
	}
	
	public function test_get_ValueIsStringWithByValueFlag_ValueReturned()
	{
		$map = $this->getSimpleMap();
		
		$value = 'someString';
		$map->set('b', $value, Type::ByValue);
		
		$this->assertSame($value, $map->get('b'));
	}
	
	public function test_get_ValueIsScalar_ValueReturned()
	{
		$map = $this->getSimpleMap();
		$map->set('a', 1);
		$this->assertEquals(1, $map->get('a'));
	}
	
	public function test_get_ValueIsCallback_MethodInvoked()
	{
		$map = $this->getSimpleMap();
		$function = function() { return 1; };
		$map->set('a', $function);
		
		$this->loader
			->expects($this->once())
			->method('get')
			->with($function);
		
		$map->get('a');
	}
	
	public function test_get_ValueIsCallbackMarkedAsByValue_MethodReturned()
	{
		$map = $this->getSimpleMap();
		$function = function() { return 1; };
		$map->set('a', $function, Type::ByValue);
		
		$this->loader->expects($this->never())->method('get');
		
		$this->assertSame($function, $map->get('a'));
	}
	
	public function test_get_ValueIsString_LoaderInvoked()
	{
		$map = $this->getSimpleMap();
		
		$value = 'SomeString';
		$map->set('b', $value, Type::Instance);
		
		$this->loader
			->expects($this->once())
			->method('get')
			->with('SomeString')
			->willReturn(1);
		
		$map->get('b');
	}
	
	public function test_get_TypeIsInstance_LoaderInvokedForEachCall()
	{
		$map = $this->getSimpleMap();
		$map->set('b', 'SomeString', Type::Instance);
		
		$this->loader
			->expects($this->exactly(2))
			->method('get')
			->willReturn(1);
		
		$map->get('b');
		$map->get('b');
	}
	
	public function test_get_TypeIsInstance_NewInstanceAlwaysReturned()
	{
		$map = $this->getSimpleMap();
		$map->set('b', 'someString', Type::Instance);
		
		$this->loader
			->expects($this->exactly(2))
			->method('get')
			->with('someString')
			->willReturnCallback(
				function() 
				{ 
					return new \stdClass();
				});
		
		$a = $map->get('b');
		$b = $map->get('b');
		
		$this->assertNotSame($a, $b);
	}
	
	public function test_get_TypeIsSingleTone_LoaderInvokedOnlyOnce()
	{
		$map = $this->getSimpleMap();
		$map->set('b', 'SomeString', Type::Singleton);
		
		$this->loader
			->expects($this->once())
			->method('get')
			->willReturn(1);
		
		$map->get('b');
		$map->get('b');
	}
	
	public function test_get_TypeIsSingleTone_SameInstanceAlwaysReturned()
	{
		$map = $this->getSimpleMap();
		$map->set('a', \stdClass::class, Type::Singleton);
		
		$this->loader
			->expects($this->once())
			->method('get')
			->willReturn(new \stdClass());
		
		$this->assertSame($map->get('a'), $map->get('a'));
	}
	
	public function test_get_ClassIsInstanceOfISingleton_SameInstanceAlwaysReturned()
	{
		$className = get_class($this->getMock(ISingleton::class));
		
		$map = $this->getSimpleMap();
		$map->set('a', $className, Type::Instance);
		
		$this->assertSame($map->get('a'), $map->get('a'));
	}
	
	public function test_get_NumberOfKeysDefined_CorrectValueReturned()
	{
		$map = $this->getSimpleMap();
		
		$a = new \stdClass();
		$b = new \stdClass();
		
		$map->set('a', $a);
		$map->set('b', $b);
		
		$this->assertNotSame($b, $map->get('a'));
		$this->assertNotSame($a, $map->get('b'));
	}
	
	public function test_get_ClassHasStaticAnnotation_ByValueFlagIsUsed()
	{
		$map = $this->getSimpleMap();
		$map->set('a', Test_SimpleMapTest_StaticAnnotation::class, Type::Instance);
		
		$this->assertEquals(Test_SimpleMapTest_StaticAnnotation::class, $map->get('a'));
	}
	
	public function test_get_ClassHasUniqueAnnotation_SingletoneFlagIsUsed()
	{
		$map = $this->getSimpleMap();
		$map->set('a', Test_SimpleMapTest_UniqueAnnotation::class, Type::Instance);
		
		$this->assertSame($map->get('a'), $map->get('a'));
	}
	
	public function test_get_ClassHasNoAnnotations_PassedFlagUsed()
	{
		$map = $this->getSimpleMap();
		$map->set('a', Test_SimpleMapTest_NoAnnotation::class, Type::Instance);
		$this->assertSame($map->get('a'), $map->get('a'));
		
		$map = $this->getSimpleMap();
		$map->set('a', Test_SimpleMapTest_NoAnnotation::class, Type::ByValue);
		$this->assertSame(Test_SimpleMapTest_NoAnnotation::class, $map->get('a'));
	}
	
	public function test_has_KeyNotDefined_ReturnFalse()
	{
		$map = $this->getSimpleMap();
		$map->set('key-1', 'value');
		
		$this->assertFalse($map->has('key'));
	}
	
	public function test_has_ValueIsClassName_ReturnTrue()
	{
		$map = $this->getSimpleMap();
		$map->set('key', \stdClass::class);
		
		$this->assertTrue($map->has('key'));
	}
	
	public function test_has_ValueIsAScalarObject_ReturnTrue()
	{
		$map = $this->getSimpleMap();
		$map->set('key', 12);
		
		$this->assertTrue($map->has('key'));
	}
	
	public function test_has_ValueStillExistsAfterFirstCall()
	{
		$map = $this->getSimpleMap();
		$map->set('key', \stdClass::class, Type::Singleton);
		$map->get('key');
		
		$this->assertTrue($map->has('key'));
	}
	
	public function test_has_ValueMArkedByValue_ReturnTrue()
	{
		$map = $this->getSimpleMap();
		$map->set('key', function() {}, Type::ByValue);
		$this->assertTrue($map->has('key'));
	}
}


/**
 * @static
 */
class Test_SimpleMapTest_StaticAnnotation
{
	
}

/**
 * @unique
 */
class Test_SimpleMapTest_UniqueAnnotation
{
	
}

/**
 * 
 */
class Test_SimpleMapTest_NoAnnotation
{
	
}