<?php
namespace Skeleton;


use Skeleton\Base\ILoader;
use Skeleton\Base\IMap;
use Skeleton\Base\IContextReference;


class UnitTestSkeletonTest extends \SkeletonTestCase
{
	/**
	 * @param IMap|\PHPUnit_Framework_MockObject_MockObject $map
	 * @return \PHPUnit_Framework_MockObject_MockObject|Skeleton
	 */
	private function mockSkeleton(&$map)
	{
		$loader = $this->getMock(ILoader::class);
		
		$map = $this->getMock(IMap::class);
		$map->method('loader')->willReturn($loader);
		
		$skeleton = $this->getMock(Skeleton::class);
		$skeleton->method('getMap')->willReturn($map);
		return $skeleton;
	}
	
	
	public function test_constructor()
	{
		$skeleton = $this->mockSkeleton($map);
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
	
	
	public function test_get_contextPassed_ContextValuesPassedToMapGetMethod()
	{
		$skeleton = $this->mockSkeleton($map);
		$testSkeleton = new UnitTestSkeleton($skeleton);
		
		$context = new Context('');
		$context->set('a', 'b');
		$contextReference = new ContextReference($context, $skeleton);

		/** @var $map \PHPUnit_Framework_MockObject_MockObject  */
		$map->expects($this->once())
			->method('get')
			->willReturnCallback(function ($a, IContextReference $context)
			{
				$this->assertEquals('b', $context->context()->get('a'));
			});

		$testSkeleton->get('n', $contextReference);
	}
	
	public function test_get_contextPassed_UnitTestMapDecoratesParentContextValues()
	{
		$skeleton = $this->mockSkeleton($map);
		$testSkeleton = new UnitTestSkeleton($skeleton);
		$testSkeleton->override('a', 'over_b');
		
		$context = new Context('');
		$context->set('a', 'b');
		$contextReference = new ContextReference($context, $skeleton);

		/** @var $map \PHPUnit_Framework_MockObject_MockObject  */
		$map->expects($this->once())
			->method('get')
			->willReturnCallback(function ($a, IContextReference $context)
			{
				$this->assertEquals('over_b', $context->context()->get('a'));
			});

		$testSkeleton->get('n', $contextReference);
	}
	
	public function test_get_contextNotPassed_NewContextCreated()
	{
		$skeleton = $this->mockSkeleton($map);
		$testSkeleton = new UnitTestSkeleton($skeleton);

		/** @var $map \PHPUnit_Framework_MockObject_MockObject  */
		$map->expects($this->once())
			->method('get')
			->with('a', $this->isInstanceOf(ContextReference::class))
			->willReturn('c');

		self::assertEquals('c', $testSkeleton->get('a'));
	}
	
	public function test_get_contextNotPassed_NewContextHasUnitTestMapValues()
	{
		$skeleton = $this->mockSkeleton($map);
		
		$testSkeleton = new UnitTestSkeleton($skeleton);
		$testSkeleton->override('a', 'b');

		/** @var $map \PHPUnit_Framework_MockObject_MockObject  */
		$map->expects($this->once())
			->method('get')
			->willReturnCallback(function ($a, IContextReference $context)
			{
				$this->assertEquals('b', $context->get('a'));
			});

		$testSkeleton->get('n');
	}
	
	public function test_get_ContextPassedAsArray_ContextCreatedFromArray()
	{
		$skeleton = $this->mockSkeleton($map);
		
		$testSkeleton = new UnitTestSkeleton($skeleton);

		/** @var $map \PHPUnit_Framework_MockObject_MockObject  */
		$map->expects($this->once())
			->method('get')
			->willReturnCallback(function ($a, IContextReference $context)
			{
				$this->assertEquals('b', $context->context()->get('a'));
			});

		$testSkeleton->get('n', ['a' => 'b']);
	}
	
	
	public function test_load_LoadOnLoaderCalled()
	{
		$testSkeleton = new UnitTestSkeleton($this->mockSkeleton($map));
		
		/** @var IMap $map */
		/** @var \PHPUnit_Framework_MockObject_MockObject $loader */
		$loader = $map->loader();
		
		$loader->expects($this->once())->method('get');

		$testSkeleton->load(\stdClass::class);
	}
	
	public function test_load_TargetItemPassedToLoader()
	{
		$testSkeleton = new UnitTestSkeleton($this->mockSkeleton($map));
		
		/** @var IMap $map */
		/** @var \PHPUnit_Framework_MockObject_MockObject $loader */
		$loader = $map->loader();
		
		$loader->method('get')->with(\stdClass::class);

		$testSkeleton->load(\stdClass::class);
	}
	
	public function test_load_ContextNotPassed_NullPassedToLoader()
	{
		$testSkeleton = new UnitTestSkeleton($this->mockSkeleton($map));
		
		/** @var IMap $map */
		/** @var \PHPUnit_Framework_MockObject_MockObject $loader */
		$loader = $map->loader();
		
		$loader->method('get')->with($this->anything(), null);

		$testSkeleton->load(\stdClass::class);
	}
	
	public function test_load_LoaderResultReturned()
	{
		$testSkeleton = new UnitTestSkeleton($this->mockSkeleton($map));
		
		/** @var IMap $map */
		/** @var \PHPUnit_Framework_MockObject_MockObject $loader */
		$loader = $map->loader();
		
		$loader->method('get')->willReturn(123);

		self::assertEquals(123, $testSkeleton->load(\stdClass::class));
	}
	
	public function test_load_ContextPassedToLoader()
	{
		$testSkeleton = new UnitTestSkeleton($this->mockSkeleton($map));
		$context = $this->getMock(IContextReference::class);
		
		/** @var IMap $map */
		/** @var \PHPUnit_Framework_MockObject_MockObject $loader */
		$loader = $map->loader();
		
		$loader->method('get')->willReturnCallback(function ($a, $b) use ($context)
		{
			self::assertEquals($context, $b);
		});

		$testSkeleton->load(\stdClass::class, $context);
	}
	
	public function test_load_ArrayAsContextPassed_ContextFromArrayCreated()
	{
		$testSkeleton = new UnitTestSkeleton($this->mockSkeleton($map));
		
		/** @var IMap $map */
		/** @var \PHPUnit_Framework_MockObject_MockObject $loader */
		$loader = $map->loader();
		
		$loader->method('get')->willReturnCallback(function ($a, IContextReference $b)
		{
			self::assertEquals(123, $b->context()->get('a'));
		});

		$testSkeleton->load(\stdClass::class, ['a' => 123]);
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