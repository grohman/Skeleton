<?php
namespace Skeleton\Tools\Crawler;


class MapperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param Mapper $map
	 */
	private function assertNoErrors(Mapper $map)
	{
		$this->assertEmpty($map->getInvalidBones());
		$this->assertEmpty($map->getInvalidSkeletons());
	}
	
	
	public function test_map_EmptySet_EmptyResultReturned()
	{
		$map = new Mapper([], []);
		$map->map();
		
		$this->assertEmpty($map->getMap());
		$this->assertNoErrors($map);
	}
	
	
	public function test_map_EmptySet_ClassAndImplementerFound()
	{
		$map = new Mapper([MapperTest_WithBone_A::class], [MapperTest_WithSkeleton_B::class]);
		$map->map();
		
		$this->assertEquals([MapperTest_WithBone_A::class => MapperTest_WithSkeleton_B::class], $map->getMap());
		$this->assertNoErrors($map);
	}
}


interface MapperTest_WithBone_A {}
class MapperTest_WithSkeleton_B implements MapperTest_WithBone_A{}