<?php
namespace Skeleton\Tools\Knot;


use PHPUnit\Framework\MockObject\MockObject;

use Skeleton\Context;
use Skeleton\Skeleton;
use Skeleton\ContextReference;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\ContextManager;


class KnotTest extends \SkeletonTestCase
{
	/** @var MockObject|ISkeletonSource */
	private $skeleton;
	
	
	private function getKnot(): Knot
	{
		/** @var ISkeletonSource skeleton */
		$this->skeleton = $this->getMock(ISkeletonSource::class);
		return (new Knot($this->skeleton));
	}
	
	private function getContextReference(): ContextReference
	{
		return new ContextReference(new Context('a'), new Skeleton());
	}
	
	/**
	 * @param mixed $value
	 */
	private function setSkeletonWillReturn($value)
	{
		$this->skeleton->method('get')->willReturn($value);
	}
	
	
	public function test_load_NoAutoload_ReturnInstance()
	{
		$knot = $this->getKnot();
		$this->assertInstanceOf(
			test_Knot_Helper_EmptyClass::class,
			$knot->load(test_Knot_Helper_EmptyClass::class, null));
	}
	
	
	public function test_load_EmptyClassWithAutoload_ReturnInstance()
	{
		$knot = $this->getKnot();
		
		$this->assertInstanceOf(
			test_Knot_Helper_AutoloadEmpty::class, 
			$knot->load(test_Knot_Helper_AutoloadEmpty::class, null));
	}
	
	
	public function test_load_Constructor()
	{
		$knot = $this->getKnot();
		
		$object = new test_Knot_Helper_Type();
		$this->setSkeletonWillReturn($object);
		
		$instance = $knot->load(test_Knot_Helper_Constructor::class, null);
		
		$this->assertSame($object, $instance->a);
	}
	
	public function test_load_Method()
	{
		$knot = $this->getKnot();
		
		$object = new test_Knot_Helper_Type();
		$this->setSkeletonWillReturn($object);
		
		$instance = $knot->load(test_Knot_Helper_Method::class, null);
		
		$this->assertSame($object, $instance->a);
	}
	
	public function test_load_Properties()
	{
		$knot = $this->getKnot();
		
		$object = new test_Knot_Helper_Type();
		$this->setSkeletonWillReturn($object);
		
		$instance = $knot->load(test_Knot_Helper_Properties::class, null);
		
		$this->assertSame($object, $instance->a);
	}
	
	public function test_load_ContextAnnotationPresent_MissingContext_ExceptionThrown()
	{
		$this->expectException(\Skeleton\Exceptions\MissingContextException::class);
		
		$knot = $this->getKnot();
		$knot->load(test_Knot_Helper_Context::class, null);
	}
	
	public function test_load_ContextAnnotationPresent_ContextSet()
	{
		$knot = $this->getKnot();
		$res = $knot->load(test_Knot_Helper_Context::class, $this->getContextReference());
		
		self::assertObjectHasAttribute(ContextManager::CONTEXT_PROPERTY_NAME, $res);
	}
	
	public function test_load_ContextAnnotationPresentInInheritedClass_ContextSet()
	{
		$knot = $this->getKnot();
		$res = $knot->load(test_Knot_Helper_ChildOfContext::class, $this->getContextReference());
		
		self::assertObjectHasAttribute(ContextManager::CONTEXT_PROPERTY_NAME, $res);
	}
	
	public function test_load_AutoloadInInheritedClassAndContextIsPresent_ContextSet()
	{
		$knot = $this->getKnot();
		$res = $knot->load(test_Knot_Helper_ChildOfAutoloadEmpty::class, $this->getContextReference());
		
		self::assertObjectHasAttribute(ContextManager::CONTEXT_PROPERTY_NAME, $res);
	}
	
	public function test_load_ContextSanityForProperty()
	{
		$knot = $this->getKnot();
		$context = new Context('');
		$context->set('a', 'b');
		
		$res = $knot->load(test_Knot_Helper_ContextProperties::class, new ContextReference($context, new Skeleton()));
		
		self::assertEquals('b', $res->a);
	}
	
	public function test_load_ContextSanityForMethod()
	{
		$knot = $this->getKnot();
		$context = new Context('');
		$context->set('a', 'b');
		
		$res = $knot->load(test_Knot_Helper_ContextMethod::class, new ContextReference($context, new Skeleton()));
		
		self::assertEquals('b', $res->a);
	}
}


class test_Knot_Helper_Type {}


class test_Knot_Helper_EmptyClass {}

/**
 * @autoload
 */
class test_Knot_Helper_AutoloadEmpty {}
class test_Knot_Helper_ChildOfAutoloadEmpty extends test_Knot_Helper_AutoloadEmpty {}

/**
 * @autoload
 */
class test_Knot_Helper_Constructor
{
	public $a;
	
	public function __construct(test_Knot_Helper_Type $a) 
	{
		$this->a = $a;
	}
}

/**
 * @autoload
 */
class test_Knot_Helper_Method
{
	public $a;
	
	/**
	 * @autoload
	 */
	public function setA(test_Knot_Helper_Type $a)
	{
		$this->a = $a;
	}
}

/**
 * @autoload
 */
class test_Knot_Helper_Properties
{
	/**
	 * @autoload
	 * @var test_Knot_Helper_Type
	 */
	public $a;
}


/**
 * @autoload
 */
class test_Knot_Helper_ContextProperties
{
	/**
	 * @context
	 */
	public $a;
}

/**
 * @autoload
 */
class test_Knot_Helper_ContextMethod
{
	public $a;
	
	/**
	 * @context
	 */
	public function setA($a)
	{
		$this->a = $a;
	}
}

/**
 * @context
 */
class test_Knot_Helper_Context {}
class test_Knot_Helper_ChildOfContext extends test_Knot_Helper_Context {}