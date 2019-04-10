<?php
namespace Skeleton\Loader;


use PHPUnit\Framework\TestCase;
use Skeleton\Base\IContextReference;
use Skeleton\Base\IContextSource;
use Skeleton\Tools\Knot\Knot;


class ValueLoaderTest extends TestCase
{
	/**
	 * @return \PHPUnit\Framework\MockObject\MockObject|Knot
	 */
	private function mockKnot()
	{
		$mock = $this->getMockBuilder(Knot::class)->disableOriginalConstructor()->getMock();
		return $mock;
	}
	
	private function getContext(): IContextReference
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return new class implements IContextReference
		{
			/** @noinspection PhpInconsistentReturnPointsInspection */
			public function context(): IContextSource {}
			public function get(string $key) {}
			public function load(string $key) {}
			public function value(string $key) {}
		};
	}
	
	
	public function test_sanity()
	{
		$l = new ValueLoader();
		$knot = $this->mockKnot();
		
		$l->setKnot($knot);
	}
	
	
	public function test_get_PassCallable_ObjectCalled()
	{
		$l = new ValueLoader();
		$item = function() { return 123; };
		
		
		self::assertEquals(123, $l->get($item));
	}
	
	public function test_get_InstancePassed_InstancePassedToKnot()
	{
		$l = new ValueLoader();
		$knot = $this->mockKnot();
		$context = $this->getContext();
		
		$l->setKnot($knot);
		
		$knot
			->expects($this->once())
			->method('loadInstance')
			->with($this, $context)
			->willReturn(123);
		
		
		self::assertEquals(123, $l->get($this, $context));
	}
	
	public function test_get_StringPassed_KnotCalled()
	{
		$l = new ValueLoader();
		$knot = $this->mockKnot();
		$context = $this->getContext();
		
		$l->setKnot($knot);
		
		$knot
			->expects($this->once())
			->method('load')
			->with('abc', $context)
			->willReturn(123);
		
		
		self::assertEquals(123, $l->get('abc', $context));
	}
}