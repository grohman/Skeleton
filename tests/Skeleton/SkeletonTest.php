<?php
namespace Skeleton;


use Skeleton\Base\IMap;
use Skeleton\Base\IConfigLoader;

use Skeleton\Exceptions;


class SkeletonTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param Skeleton $s
	 * @return \PHPUnit_Framework_MockObject_MockObject|IMap
	 */
	private function mockMap(Skeleton $s) 
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|IMap $map */
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
	 * @return \PHPUnit_Framework_MockObject_MockObject|IConfigLoader
	 */
	private function mockLoader(Skeleton $s) 
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|IConfigLoader $loader */
		$loader = $this->getMock(IConfigLoader::class);
		$s->setConfigLoader($loader);
		return $loader;
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
		
		$s->get('a\b');
	}
	
	public function test_get_NotClassInMap_ConfigLoaderCalled()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockLoader($s);
		
		$loader->expects($this->atLeastOnce())->method('tryLoad');
		
		$s->get('a\b');
	}
	
	public function test_get_ConfigLoaderCalledWithCorrectValues()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockLoader($s);
		
		$loader->expects($this->once())->method('tryLoad')->with('a');
		
		$s->get('a\b');
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_NotClassInMapAdnConfigLoaderIsNull_ErrorThrown()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$s->setConfigLoader(null);
		
		$s->get('a\b');
	}
	
	public function test_get_GetCalledOneMoreTimeAfterConfigLoaded()
	{
		$s = new Skeleton();
		$map = $this->mockMapHasValue($s, false);
		$this->mockLoader($s);
		
		// Has method will return false by default so get should only be called once. 
		$map->expects($this->once())->method('get')->with('some\complex\namespace');
		
		
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
	
	public function test_setConfigLoader_SetToNull() 
	{
		$s = new Skeleton();
		$s->setConfigLoader(null);
		$this->assertNull($s->getConfigLoader());
	}
}