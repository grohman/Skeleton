<?php
namespace Skeleton\Maps;


use PHPUnit\Framework\MockObject\MockObject;

use Skeleton\Type;
use Skeleton\Base\IMap;


class TestMapTest extends \SkeletonTestCase
{
	/**
	 * @return MockObject|IMap
	 */
	private function mockIMap()
	{
		return $this->getMock(IMap::class);
	}
	
	
	public function test_getOriginal() 
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		
		$this->assertSame($map, $testMap->getOriginal());
	}
	
	
	public function test_set_RedirectedToMain() 
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		
		$map->expects($this->once())
			->method('set')
			->with('a', \stdClass::class, Type::Singleton);
		
		$testMap->set('a', \stdClass::class, Type::Singleton);
	}
	
	
	public function test_forceSet_RedirectedToMain()
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		
		$map->expects($this->once())
			->method('forceSet')
			->with('a', \stdClass::class, Type::Singleton);
		
		$testMap->forceSet('a', \stdClass::class, Type::Singleton);
	}
	
	
	public function test_override()
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		$testMap->override('a', 'b');
		
		$this->assertEquals('b', $testMap->get('a'));
	}
	
	public function test_override_OverrideValueReturned()
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		$testMap->override('a', 'b');
		
		$map->expects($this->never())->method('get');
		
		$testMap->get('a');
	}
	
	
	public function test_get_NoOverrideObject_MainMapCalled() 
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		$testMap->override('c', \stdClass::class);
		
		$map->expects($this->once())
			->method('get')
			->with('a');
		
		$testMap->get('a');
	}
	
	public function test_get_HasOverrideObject_MainMapNotCalled() 
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		$testMap->override('a', \stdClass::class);
		
		$map->expects($this->never())
			->method('get')
			->with('a');
		
		$testMap->get('a');
	}
	
	
	public function test_has_NoObjectInTestOrMain_ReturnFalse() 
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		
		$testMap->override('b', new \stdClass());
		
		$this->assertFalse($testMap->has('a'));
	}
	
	public function test_has_ObjectInTestMap_ReturnTrue() 
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		
		$testMap->override('a', new \stdClass());
		
		$this->assertTrue($testMap->has('a'));
	}
	
	public function test_has_ObjectInMainMap_ReturnTrue() 
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		
		$map->method('has')
			->with('a')
			->willReturn(true);
		
		$testMap->override('b', new \stdClass());
		
		$this->assertTrue($testMap->has('a'));
	}
}