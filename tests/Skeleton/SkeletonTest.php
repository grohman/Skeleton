<?php
namespace Skeleton;


use Skeleton\Base\IMap;
use Skeleton\Base\ILoader;
use Skeleton\Base\IConfigLoader;

use Skeleton\Maps\SimpleMap;


class SkeletonTest extends \SkeletonTestCase
{
	/**
	 * @param Skeleton $s
	 * @return \PHPUnit_Framework_MockObject_MockObject|IMap
	 */
	private function mockMap(Skeleton $s) 
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|IMap $map */
		$map = $this->getMock(IMap::class);
		$map->method('loader')->willReturn($this->mockLoader());
		$s->setMap($map);
		return $map;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|ILoader
	 */
	private function mockLoader()
	{
		return $this->getMock(ILoader::class);
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|GlobalSkeleton
	 */
	private function mockGlobalSkeleton()
	{
		$global = $this->getMockBuilder(GlobalSkeleton::class)
			->disableOriginalConstructor()
			->getMock();
		
		$ref = new \ReflectionClass(GlobalSkeleton::class);
		$instanceProperty = $ref->getProperty('instance');
		$instanceProperty->setAccessible(true);
		$instanceProperty->setValue(null, $global);
		
		return $global;
	}
	
	/**
	 * @param Skeleton $s
	 * @param bool $has
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockMapHasValue(Skeleton $s, $has = false) 
	{
		$map = $this->mockMap($s);
		$map->method('has')->willReturn($has);
		return $map;
	}
	
	/**
	 * @param Skeleton $s
	 * @return \PHPUnit_Framework_MockObject_MockObject|IConfigLoader
	 */
	private function mockConfigLoader(Skeleton $s) 
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|IConfigLoader $loader */
		$loader = $this->getMock(IConfigLoader::class);
		$s->setConfigLoader($loader);
		return $loader;
	}
	
	
	protected function tearDown()
	{
		$ref = new \ReflectionClass(GlobalSkeleton::class);
		$instanceProperty = $ref->getProperty('instance');
		$instanceProperty->setAccessible(true);
		$instanceProperty->setValue(null, null);
	}
	
	
	public function test_SelfReturned() 
	{
		$s = new Skeleton();
		
		/** @var \PHPUnit_Framework_MockObject_MockObject|IMap $map */
		$map = $this->getMock(IMap::class);
		
		/** @var \PHPUnit_Framework_MockObject_MockObject|IConfigLoader $loader */
		$loader = $this->getMock(IConfigLoader::class);
		
		$this->assertSame($s, $s->setMap($map));
		$this->assertSame($s, $s->setConfigLoader($loader));
		$this->assertSame($s, $s->set('a', 'b'));
		$this->assertSame($s, $s->enableKnot());
	}
	
	
	/**
	 * @expectedException \Skeleton\Exceptions\InvalidKeyException
	 */
	public function test_get_KeyIsNotString_ErrorThrown()
	{
		$s = new Skeleton();
		$s->get(12);
	}
	
	public function test_get_MapAlreadyHasClass_GetOnMapCalled()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, true);
		$loader = $this->mockConfigLoader($s);
		
		$loader->expects($this->never())->method('tryLoad');
		
		$s->get('a\b');
	}
	
	/**
	 * Ignore missing value, this is not tested by this unit test.
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_NotClassInMap_ConfigLoaderCalled()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockConfigLoader($s);
		
		$loader->expects($this->atLeastOnce())->method('tryLoad');
		
		$s->get('a\b');
	}
	
	/**
	 * Ignore missing value, this is not tested by this unit test.
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_ConfigLoaderCalledWithCorrectValues()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockConfigLoader($s);
		
		$loader->expects($this->once())->method('tryLoad')->with('a');
		
		$s->get('a\b');
	}
	
	public function test_get_ValueExistsAfterConfigLoaderIsUsed_LoadedValueReturned()
	{
		$s = new Skeleton();
		$loader = $this->mockConfigLoader($s);
		$s->setMap(new SimpleMap());
		
		$loader
			->expects($this->once())
			->method('tryLoad')
			->willReturnCallback(
				function() 
					use ($s) 
				{
					$s->set('some\complex\namespace', 123);
					return true;
				}
			);
		
		$this->assertSame(123, $s->get('some\complex\namespace'));
	}
	
	
	/**
	 * Ignore missing value, this is not tested by this unit test.
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_SkeletonUseGlobalFlagNotSet_GlobalNotCalled()
	{
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		
		$global->expects($this->never())->method('get');
		
		$s->get('a');
	}
	
	public function test_get_SkeletonUseGlobalFlagSet_GlobalSkeletonCalled()
	{
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		
		$global->expects($this->once())->method('get');
		
		$s->useGlobal();
		
		$s->get('a');
	}
	
	public function test_get_SkeletonUseGlobalFlagSet_KeyPassedToGlobalSkeleton()
	{
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		
		$global->expects($this->once())->method('get')->with('a');
		
		$s->useGlobal();
		
		$s->get('a');
	}
	
	/**
	 * Ignore missing value, this is not tested by this unit test.
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_FunctionUseGlobalFlagIsFalse_GlobalNotCalled()
	{
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		$s->useGlobal();
		
		$global->expects($this->never())->method('get');
		
		$s->get('a', null, true);
	}
	
	public function test_get_ObjectReturnedFromGlobal_NoErrorThrown()
	{
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		$s->useGlobal();
		
		$global->method('get')->willReturn(123);
		
		$this->assertSame(123, $s->get('a'));
	}
	
	
	/**
	 * @expectedException \Skeleton\Exceptions\InvalidKeyException
	 */
	public function test_set_KeyIsNotString_ErrorThrown()
	{
		$s = new Skeleton();
		$s->set(12, "a");
	}
	
	public function test_set_ReturnsSelf()
	{
		$s = new Skeleton();
		$this->assertSame($s, $s->set('a', 'b'));
	}
	
	public function test_set_SetOnMapCalled()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$map->expects($this->once())
			->method('set')
			->with('a', 'b', Type::ByValue);
		
		$s->set('a', 'b', Type::ByValue);
	}
	
	public function test_set_KeyIsArray_AllValuesAdded()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$map->expects($this->at(0))
			->method('set')
			->with('a', 'val', Type::ByValue);
		
		$map->expects($this->at(1))
			->method('set')
			->with('b', 'val', Type::ByValue);
		
		$s->set(['a', 'b'], 'val', Type::ByValue);
	}
	
	public function test_set_KeyIsArray_SelfReturned()
	{
		$s = new Skeleton();
		$this->assertSame($s, $s->set(['a', 'b'], 'val'));
	}
	
	
	public function test_setMap_MapSet() 
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$this->assertSame($map, $s->getMap());
	}
	
	
	public function test_setConfigLoader_LoaderSet() 
	{
		$s = new Skeleton();
		$loader = $this->mockConfigLoader($s);
		
		$this->assertSame($loader, $s->getConfigLoader());
	}
	
	public function test_setConfigLoader_SetToNull() 
	{
		$s = new Skeleton();
		$s->setConfigLoader(null);
		$this->assertNull($s->getConfigLoader());
	}
	
	
	public function test_enableKnot_EnableKnotOnMapCalled()
	{
		$s = new Skeleton();
		
		$map = $this->mockMap($s);
		$map->expects($this->once())
			->method('enableKnot')
			->with($s);
		
		$s->enableKnot();
	}
	
	
	public function test_load_LoadCalledOnMapsLoader()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		/** @var \PHPUnit_Framework_MockObject_MockObject $loader */
		$loader = $map->loader();
		$loader->expects($this->once())->method('get')->with('abc')->willReturn(123);
		
		$this->assertEquals(123, $s->load('abc'));
	}
	
	
	public function testSanity_knotNotEnabled()
	{
		$s = new Skeleton();
		
		$s->set(SkeletonTest_Helper_A::class, SkeletonTest_Helper_A::class);
		$s->set(SkeletonTest_Helper_B::class, SkeletonTest_Helper_B::class);
		
		/** @var SkeletonTest_Helper_B $a */
		$a = $s->get(SkeletonTest_Helper_B::class);
		
		$this->assertNull($a->a);
	}
}


class SkeletonTest_Helper_A {}


/**
 * @autoload
 */
class SkeletonTest_Helper_B
{
	/**
	 * @autoload
	 * @var SkeletonTest_Helper_A
	 */
	public $a;
}