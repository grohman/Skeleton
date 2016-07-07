<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Base\ISkeletonSource;


class KnotTest extends \PHPUnit_Framework_TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|ISkeletonSource */
	private $skeleton;
	
	
	/**
	 * @return Knot
	 */
	private function getKnot()
	{
		/** @var ISkeletonSource skeleton */
		$this->skeleton = $this->getMock(ISkeletonSource::class);
		return (new Knot($this->skeleton));
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
			$knot->load(test_Knot_Helper_EmptyClass::class));
	}
	
	
	public function test_load_EmptyClassWithAutoload_ReturnInstance()
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