<?php
namespace Skeleton;


use Skeleton\Base\IMap;


class UnitTestSkeletonTest extends \SkeletonTestCase
{
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|Skeleton
	 */
	private function mockSkeleton()
	{
		$map = $this->getMock(IMap::class);
		$skeleton = $this->getMock(Skeleton::class);
		$skeleton->method('getMap')->willReturn($map);
		return $skeleton;
	}
	
	
	public function test_constructor()
	{
		$skeleton = $this->mockSkeleton();
		$skeleton->expects($this->once())->method('setMap');
		new UnitTestSkeleton($skeleton);
	}
	
	public function test_get_and_override()
	{
		$skeleton = new Skeleton();
		$testSkeleton = new UnitTestSkeleton($skeleton);
		
		$testSkeleton->override('a', 'b');
		$this->assertEquals('b', $testSkeleton->get('a'));
	}
	
	public function test_override_CalledTwice_NewValueSet()
	{
		$skeleton = new Skeleton();
		$testSkeleton = new UnitTestSkeleton($skeleton);
		
		$testSkeleton->override('a', 'b');
		$testSkeleton->override('a', 'c');
		
		$this->assertEquals('c', $testSkeleton->get('a'));
	}
	
	
	/**
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_clear_ValueReset()
	{
		$skeleton = new Skeleton();
		$testSkeleton = new UnitTestSkeleton($skeleton);
		
		$testSkeleton->override('a', 'b');
		$testSkeleton->clear();
		
		$testSkeleton->get('a');
	}
	
	
	public function test_sanity_TestMapIsUsed()
	{
		$skeleton = new Skeleton();
		$testSkeleton = new UnitTestSkeleton($skeleton);
		
		$testSkeleton->override('a', 'b');
		
		$this->assertEquals('b', $skeleton->get('a'));
	}
}