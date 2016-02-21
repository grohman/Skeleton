<?php
namespace tests\Skeleton;


use \Skeleton\Type;
use \Skeleton\Skeleton;
use \Skeleton\Base\IMap;
use \Skeleton\Base\IConfigLoader;

use \Skeleton\Exceptions;


class SkeletonTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param Skeleton $s
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockMap(Skeleton $s) 
	{
		$map = $this->getMock(IMap::class);
		$s->setMap($map);
		return $map;
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
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockLoader(Skeleton $s) 
	{
		$loader = $this->getMock(IConfigLoader::class);
		$s->setConfigLoader($loader);
		return $loader;
	}
	
	
	public function test_SelfReturned() 
	{
		$s = new Skeleton();
		$map = $this->getMock(IMap::class);
		$loader = $this->getMock(IConfigLoader::class);
		
		$this->assertSame($s, $s->setMap($map));
		$this->assertSame($s, $s->setConfigLoader($loader));
		$this->assertSame($s, $s->set('a', 'b'));
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
		$loader = $this->mockLoader($s);
		
		$loader->expects($this->never())->method('tryLoad');
		
		$s->get('a');
	}
	
	public function test_get_NotClassInMap_ConfigLoaderCalled()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockLoader($s);
		
		$loader->expects($this->atLeastOnce())->method('tryLoad');
		
		$s->get('a');
	}
	
	public function test_get_MapAlreadyHasClass_ConfigLoaderNotInvoked()
	{
		$s = new Skeleton();
		$map = $this->mockMapHasValue($s, true);
		
		$map->method('has')->willReturn(true);
		$map->expects($this->once())->method('get')->with('a');
		
		$s->get('a');
	}
	
	public function test_get_ConfigLoaderCalledWithCorrectValues()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockLoader($s);
		
		$loader->expects($this->once())->method('tryLoad')->with('a');
		
		$s->get('a');
	}
	
	public function test_get_ComplexKey_LoaderCalledForEachPart()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockLoader($s);
		
		$loader->expects($this->at(0))->method('tryLoad')->with('some/complex/namespace');
		$loader->expects($this->at(1))->method('tryLoad')->with('some/complex');
		$loader->expects($this->at(2))->method('tryLoad')->with('some');
		
		$s->get('some\complex\namespace');
	}
	
	public function test_get_GetCalledOneMoreTimeAfterConfigLoaded()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		$this->mockLoader($s);
		
		$map->expects($this->once())->method('get')->with('some\complex\namespace');
		
		
		$s->get('some\complex\namespace');
	}
	
	public function test_get_ConfigFound_StopLoadingConfigs()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		$loader = $this->mockLoader($s);
		
		$map->expects($this->at(0))->method('has')->willReturn(false);
		$map->expects($this->at(1))->method('has')->willReturn(true);
		$map->expects($this->exactly(2))->method('has')->willReturn(true);
		
		$loader->expects($this->at(0))->method('tryLoad')->with('some/complex/namespace')->willReturn(false);
		$loader->expects($this->at(1))->method('tryLoad')->with('some/complex')->willReturn(true);
		$loader->expects($this->exactly(2))->method('tryLoad');
		
		$s->get('some\complex\namespace');
	}
	
	
	/**
	 * @expectedException \Skeleton\Exceptions\InvalidKeyException
	 */
	public function test_set_KeyIsNotString_ErrorThrown()
	{
		$s = new Skeleton();
		$s->set(12, "a");
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\InvalidImplementerException
	 */
	public function test_set_ImplementerIsNotObjectOrString_ErrorThrown()
	{
		$s = new Skeleton();
		$s->set("a", 12);
	}
	
	public function test_set_SetOnMapCalled()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$map->expects($this->once())
			->method('set')
			->with('a', 'b', Type::StaticClass);
		
		$s->set('a', 'b', Type::StaticClass);
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
		$loader = $this->mockLoader($s);
		
		$this->assertSame($loader, $s->getConfigLoader());
	}
}