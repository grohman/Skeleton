<?php
namespace Skeleton;


use Skeleton\Base\ISkeletonSource;


class GlobalSkeletonTest extends \SkeletonTestCase
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
	
	
	public function test_getSkeleton_Empty_ReturnFalse()
	{
		$this->assertNull(GlobalSkeleton::instance()->getSkeleton('abcd'));
	}
	
	public function test_getSkeleton_NotFound_ReturnNull()
	{
		GlobalSkeleton::instance()->add('123', $this->mockISkeletonSource());
		GlobalSkeleton::instance()->add('456', $this->mockISkeletonSource());
		
		$this->assertNull(GlobalSkeleton::instance()->getSkeleton('abc'));
	}
	
	public function test_getSkeleton_HAveSkeletonWithSameStart_ReturnNull()
	{
		GlobalSkeleton::instance()->add('SameStart1', $this->mockISkeletonSource());
		
		$this->assertNull(GlobalSkeleton::instance()->getSkeleton('SameStart2'));
	}
	
	public function test_getSkeleton_PrefixShorterThan3Characters_ReturnNull()
	{
		GlobalSkeleton::instance()->add('abc', $this->mockISkeletonSource());
		
		$this->assertNull(GlobalSkeleton::instance()->getSkeleton('ab'));
	}
	
	public function test_getSkeleton_HaveSimilar_ReturnCorrect()
	{
		$target = $this->mockISkeletonSource();
		GlobalSkeleton::instance()->add('abcdefg1', $this->mockISkeletonSource());
		GlobalSkeleton::instance()->add('abcdefg2', $target);
		GlobalSkeleton::instance()->add('abcdefg3', $this->mockISkeletonSource());
		
		$this->assertSame($target, GlobalSkeleton::instance()->getSkeleton('abcdefg2'));
	}
	
	
	/**
	 *  @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_NoSkeletonsDefined_ErrorThrown()
	{
		GlobalSkeleton::instance()->add('b', $this->mockISkeletonSource());
		
		GlobalSkeleton::instance()->get('a');
	}
	
	public function test_get_SkeletonsDefined_GetForSkeletonCalled()
	{
		$source = $this->mockISkeletonSource();
		GlobalSkeleton::instance()->add('a', $source);
		
		$source->expects($this->once())->method('get');
		
		GlobalSkeleton::instance()->get('a');
	}
	
	public function test_get_SkeletonsDefined_CorrectParamsPassed()
	{
		$source = $this->mockISkeletonSource();
		GlobalSkeleton::instance()->add('a', $source);
		
		$source->method('get')->with('a');
		
		GlobalSkeleton::instance()->get('a');
	}
	
	public function test_get_SkeletonsDefined_ReturnedValueReturnedByGlobal()
	{
		$source = $this->mockISkeletonSource();
		GlobalSkeleton::instance()->add('a', $source);
		
		$source->method('get')->willReturn(123);
		
		$this->assertSame(123, GlobalSkeleton::instance()->get('a'));
	}
	
	
	public function test_add_PrefixShorterThan3Characters_ItemFound()
	{
		$source = $this->mockISkeletonSource();
		GlobalSkeleton::instance()->add('ac', $source);
		
		$this->assertSame($source, GlobalSkeleton::instance()->getSkeleton('ac'));
	}
}