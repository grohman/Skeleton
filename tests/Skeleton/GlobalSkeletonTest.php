<?php
namespace Skeleton;


use Skeleton\Base\ISkeletonSource;


class GlobalSkeletonTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|ISkeletonSource
	 */
	private function mockISkeletonSource()
	{
		return $this->getMock(ISkeletonSource::class);
	}
	
	
	protected function setUp()
	{
		$p = (new \ReflectionClass(GlobalSkeleton::class))->getProperty('skeletons');
		$p->setAccessible(true);
		$p->setValue(GlobalSkeleton::instance(), []);
		
	}
	
	
	public function test_get_Empty_ReturnFalse()
	{
		$this->assertNull(GlobalSkeleton::instance()->get('abcd'));
	}
	
	public function test_get_NotFound_ReturnNull()
	{
		GlobalSkeleton::instance()->add('123', $this->mockISkeletonSource());
		GlobalSkeleton::instance()->add('456', $this->mockISkeletonSource());
		
		$this->assertNull(GlobalSkeleton::instance()->get('abc'));
	}
	
	public function test_get_HAveSkeletonWithSameStart_ReturnNull()
	{
		GlobalSkeleton::instance()->add('SameStart1', $this->mockISkeletonSource());
		
		$this->assertNull(GlobalSkeleton::instance()->get('SameStart2'));
	}
	
	public function test_get_PrefixShorterThan3Characters_ReturnNull()
	{
		GlobalSkeleton::instance()->add('abc', $this->mockISkeletonSource());
		
		$this->assertNull(GlobalSkeleton::instance()->get('ab'));
	}
	
	public function test_get_HaveSimilar_ReturnCorrect()
	{
		$target = $this->mockISkeletonSource();
		GlobalSkeleton::instance()->add('abcdefg1', $this->mockISkeletonSource());
		GlobalSkeleton::instance()->add('abcdefg2', $target);
		GlobalSkeleton::instance()->add('abcdefg3', $this->mockISkeletonSource());
		
		$this->assertSame($target, GlobalSkeleton::instance()->get('abcdefg2'));
	}
	
	
	public function test_add_PrefixShorterThan3Characters_ItemFound()
	{
		$source = $this->mockISkeletonSource();
		GlobalSkeleton::instance()->add('ac', $source);
		
		$this->assertSame($source, GlobalSkeleton::instance()->get('ac'));
	}
}