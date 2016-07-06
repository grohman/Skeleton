<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Skeleton;


class KnotTest extends \PHPUnit_Framework_TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|Skeleton */
	private $skeleton;
	
	
	/**
	 * @return Knot
	 */
	private function getKnot()
	{
		/** @var Skeleton skeleton */
		$this->skeleton = $this->getMock(Skeleton::class);
		return (new Knot())->setSkeleton($this->skeleton);
	}
	
	/**
	 * @param mixed $value
	 */
	private function setSkeletonWillReturn($value)
	{
		$this->skeleton->method('get')->willReturn($value);
	}
	
	
	public function test_setSkeleton_ReturnSelf()
	{
		/** @var Skeleton $skeleton */
		$skeleton = $this->getMock(Skeleton::class);
		$obj = new Knot();
		
		$this->assertSame($obj, $obj->setSkeleton($skeleton));
	}
	
	
	public function test_load_NoAutoload_ReturnFalse()
	{
		$knot = $this->getKnot();
		$this->assertFalse($knot->load(test_Knot_Helper_EmptyClass::class));
	}
	
	
	public function test_load_EmptyClassWithAutoload_ReturnInstace()
	{
		$knot = $this->getKnot();
		
		$this->assertInstanceOf(
			test_Knot_Helper_AutoloadEmpty::class, 
			$knot->load(test_Knot_Helper_AutoloadEmpty::class));
	}
	
	
	public function test_load_Constructor()
	{
		$knot = $this->getKnot();
		
		$object = new test_Knot_Helper_Type();
		$this->setSkeletonWillReturn($object);
		
		$instance = $knot->load(test_Knot_Helper_Constructor::class);
		
		$this->assertSame($object, $instance->a);
	}
	
	public function test_load_Method()
	{
		$knot = $this->getKnot();
		
		$object = new test_Knot_Helper_Type();
		$this->setSkeletonWillReturn($object);
		
		$instance = $knot->load(test_Knot_Helper_Method::class);
		
		$this->assertSame($object, $instance->a);
	}
	
	public function test_load_Properties()
	{
		$knot = $this->getKnot();
		
		$object = new test_Knot_Helper_Type();
		$this->setSkeletonWillReturn($object);
		
		$instance = $knot->load(test_Knot_Helper_Properties::class);
		
		$this->assertSame($object, $instance->a);
	}
}


class test_Knot_Helper_Type {}


class test_Knot_Helper_EmptyClass {}

/**
 * @autoload
 */
class test_Knot_Helper_AutoloadEmpty {}

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