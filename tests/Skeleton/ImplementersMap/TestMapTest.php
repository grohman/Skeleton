<?php
namespace test\Skeleton\ImplementersMap;


use Skeleton\Type;
use Skeleton\Base\IMap;
use Skeleton\ImplementersMap\TestMap;


class TestMapTest extends \PHPUnit_Framework_TestCase
{
	public function test_getMainMap() 
	{
		$map = $this->mockIMap();
		$testMap = new TestMap($map);
		
		$this->assertSame($map, $testMap->getMainMap());
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
	
	public function test_get_HasOverrideObject_MainMapCalled() 
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
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IMap
	 */
	private function mockIMap() {
		return $this->getMock(IMap::class);
	}
}