<?php
namespace Skeleton;


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
		
		/** @var Context|\PHPUnit_Framework_MockObject_MockObject $context */
		$context = self::getMockBuilder(Context::class)->getMock();
		
		
		$subject = new ContextReference($context, $skeleton);
		
		
		$context->expects($this->once())->method('get')->with('a')->willReturn(123);
		self::assertEquals(123, $subject->value('a'));
	}
	
	public function test_get_SkeletonInvoked()
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|Skeleton $skeleton */
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
}