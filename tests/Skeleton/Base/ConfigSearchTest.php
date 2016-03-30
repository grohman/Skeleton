<?php
namespace Skeleton\Base;


class ConfigSearchTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IMap
	 */
	private function mockMap() 
	{
		return $this->getMock(IMap::class);
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IConfigLoader
	 */
	private function mockLoader() 
	{
		return $this->getMock(IConfigLoader::class);
	}
	
	
	public function test_get_ConfigLoaderCalledWithCorrectValues()
	{
		$map = $this->mockMap();
		$loader = $this->mockLoader();
		
		$loader->expects($this->once())->method('tryLoad')->with('a');
		
		ConfigSearch::searchFor('a\b', $map, $loader);
	}
	
	public function test_get_ComplexKey_LoaderCalledForEachPart()
	{
		$map = $this->mockMap();
		$loader = $this->mockLoader();
		
		$loader->expects($this->at(0))->method('tryLoad')->with('some/complex/namespace');
		$loader->expects($this->at(1))->method('tryLoad')->with('some/complex');
		$loader->expects($this->at(2))->method('tryLoad')->with('some');
		
		ConfigSearch::searchFor('some\complex\namespace\cls', $map, $loader);
	}
	
	public function test_get_ConfigFound_StopLoadingConfigs()
	{
		$map = $this->mockMap();
		$loader = $this->mockLoader();
		
		$map->expects($this->at(0))->method('has')->willReturn(true);
		$map->expects($this->exactly(1))->method('has')->willReturn(true);
		
		$loader->expects($this->at(0))->method('tryLoad')->with('some/complex/namespace')->willReturn(false);
		$loader->expects($this->at(1))->method('tryLoad')->with('some/complex')->willReturn(true);
		$loader->expects($this->exactly(2))->method('tryLoad')->willReturn(true);
		
		ConfigSearch::searchFor('some\complex\namespace\cls', $map, $loader);
	}
	
	public function test_get_CalledForClassNotInNamespace_LoaderCalledForGlobal()
	{
		$map = $this->mockMap();
		$loader = $this->mockLoader();
		
		$loader->expects($this->exactly(1))->method('tryLoad')->with('Global');
		
		ConfigSearch::searchFor('cls', $map, $loader);
	}
}