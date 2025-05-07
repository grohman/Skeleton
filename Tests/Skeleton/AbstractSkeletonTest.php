<?php
namespace Skeleton;


use PHPUnit\Framework\TestCase;


class AbstractSkeletonHelper extends AbstractSkeleton
{
	protected static $skeleton = null;
	
	public static $skeletonToReturn = null;
	public static $skeletonPassedToConfig = null;
	public static $createCallsCount = 0;
	
	
	protected static function createSkeleton(): Skeleton
	{
		self::$createCallsCount++;
		return self::$skeletonToReturn ?: parent::createSkeleton();
	}
	
	protected static function configureSkeleton(Skeleton $s)
	{
		self::$skeletonPassedToConfig = $s;
	}
	
	
	public static function reset()
	{
		self::$createCallsCount = 0;
		self::$skeletonToReturn = null;
		self::$skeletonPassedToConfig = null;
		
		self::$skeleton = null;
	}
}

class AbstractSkeletonExtraHelper extends AbstractSkeleton
{
	protected static $skeleton = null;
}

class AbstractSkeletonInvalidSetupHelper extends AbstractSkeleton
{
	
}


class AbstractSkeletonTest extends TestCase
{
	public function setUp()
	{
		AbstractSkeletonHelper::reset();
	}
	
	
	public function test_sanity()
	{
		self::assertSame(AbstractSkeletonExtraHelper::skeleton(), AbstractSkeletonExtraHelper::skeleton());
		self::assertNotSame(AbstractSkeletonHelper::skeleton(), AbstractSkeletonExtraHelper::skeleton());
	}
	
	
	/**
	 * @expectedException \Skeleton\Exceptions\StaticSkeletonPropertyMissing
	 */
	public function test_SkeletonPropertyMissing_ExceptionThrown()
	{
		AbstractSkeletonInvalidSetupHelper::skeleton();
	}
	
	
	public function test_skeleton_InvokeWithoutParams_SkeletonInstanceReturned()
	{
		$s = AbstractSkeletonHelper::skeleton();
		self::assertInstanceOf(Skeleton::class, $s);
	}
	
	public function test_skeleton_InvokeWithoutParams_SameSkeletonReturned()
	{
		self::assertSame(
			AbstractSkeletonHelper::skeleton(),
			AbstractSkeletonHelper::skeleton()
		);
	}
	
	public function test_createSkeleton_SkeletonReturnedByFunctionIsUsed()
	{
		$s = new Skeleton();
		
		
		AbstractSkeletonHelper::$skeletonToReturn = $s;
		self::assertSame($s, AbstractSkeletonHelper::skeleton());
		
		
		AbstractSkeletonHelper::skeleton();
		self::assertEquals(1, AbstractSkeletonHelper::$createCallsCount);
	}
	
	
	public function test_configureSkeleton_MethodCalledOnlyOnce()
	{
		$s = AbstractSkeletonHelper::skeleton();
		
		
		self::assertSame($s, AbstractSkeletonHelper::$skeletonPassedToConfig);
		
		
		AbstractSkeletonHelper::$skeletonPassedToConfig = null;
		AbstractSkeletonHelper::skeleton();
		
		
		self::assertEquals(1, AbstractSkeletonHelper::$createCallsCount);
	}
	
	public function test_configureSkeleton_CalledOnlyOnce()
	{
		AbstractSkeletonHelper::skeleton();
		AbstractSkeletonHelper::$skeletonPassedToConfig = null;
		AbstractSkeletonHelper::skeleton();
		
		
		self::assertNull(AbstractSkeletonHelper::$skeletonPassedToConfig);
	}
	
	
	public function test_skeleton_KeyPassed_ReturnValue()
	{
		AbstractSkeletonHelper::skeleton()->setValue('a', 'b');
		self::assertEquals('b', AbstractSkeletonHelper::skeleton('a'));
	}
	
	public function test_load_sanity()
	{
		AbstractSkeletonHelper::skeleton()->enableKnot();
		AbstractSkeletonHelper::skeleton()->set(AbstractSkeletonHelp_A::class, AbstractSkeletonHelp_A::class);
		
		
		$result = AbstractSkeletonHelper::load(AbstractSkeletonHelp_B::class);
		
		
		self::assertInstanceOf(AbstractSkeletonHelp_B::class, $result);
		self::assertInstanceOf(AbstractSkeletonHelp_A::class, $result->a);
	}
}


class AbstractSkeletonHelp_A {}

/**
 * @autoload
 */
class AbstractSkeletonHelp_B 
{
	/**
	 * @autoload
	 * @var \Skeleton\AbstractSkeletonHelp_A
	 */
	public $a;
}