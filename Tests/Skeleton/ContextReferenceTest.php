<?php
namespace Skeleton;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


class ContextReferenceTest extends TestCase
{
	public function test_context_ContextReturned()
	{
		$skeleton = new Skeleton();
		$context = new Context('a');
		
		$subject = new ContextReference($context, $skeleton);
		
		self::assertEquals($context, $subject->context());
	}
	
	public function test_value_ContextCalled()
	{
		$skeleton = new Skeleton();
		
		/** @var Context|MockObject $context */
		$context = self::getMockBuilder(Context::class)->getMock();
		
		
		$subject = new ContextReference($context, $skeleton);
		
		
		$context->expects($this->once())->method('get')->with('a')->willReturn(123);
		self::assertEquals(123, $subject->value('a'));
	}
	
	public function test_get_SkeletonInvoked()
	{
		/** @var MockObject|Skeleton $skeleton */
		$skeleton = self::getMockBuilder(Skeleton::class)->getMock();
		$context = new Context('a');
		
		
		$subject = new ContextReference($context, $skeleton);
		
		
		$skeleton->expects($this->once())->method('get')->with('val', $subject, false)->willReturn(123);
		self::assertEquals(123, $subject->get('val'));
	}
	
	public function test_DebugInfo()
	{
		$skeleton = new Skeleton();
		$context = new Context('a');
		
		
		$subject = new ContextReference($context, $skeleton);
		
		
		self::assertEquals(['context' => 'a'], $subject->__debugInfo($subject, true));
	}
	
	
	public function test_load_SkeletonPassed()
	{
		/** @var MockObject|Skeleton $skeleton */
		$skeleton = self::getMockBuilder(Skeleton::class)->getMock();
		
		
		$subject = new ContextReference(new Context(), $skeleton);
		$skeleton->expects($this->once())
			->method('load')
			->with(
				$this->anything(), 
				$subject
			);
		
		
		$subject->load(\stdClass::class);
	}
	
	public function test_load_KeyPassed()
	{
		/** @var MockObject|Skeleton $skeleton */
		$skeleton = self::getMockBuilder(Skeleton::class)->getMock();
		
		
		$subject = new ContextReference(new Context(), $skeleton);
		$skeleton->expects($this->once())
			->method('load')
			->with(
				'hello',
				$this->anything() 
			);
		
		
		$subject->load('hello');
	}
	
	public function test_load_SkeletonResultReturned()
	{
		/** @var MockObject|Skeleton $skeleton */
		$skeleton = self::getMockBuilder(Skeleton::class)->getMock();
		
		$subject = new ContextReference(new Context(), $skeleton);
		$skeleton->method('load')->willReturn(123);
		
		self::assertEquals(123, $subject->load('hello'));
	}
}