<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Annotation\Extractor;


class ConstructorConnectorTest extends \PHPUnit_Framework_TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|ISkeletonSource */
	private $skeleton;
	
	
	/**
	 * @return ConstructorConnector
	 */
	private function getConstructorConnector()
	{
		$this->skeleton = $this->getMock(ISkeletonSource::class);
		return (new ConstructorConnector())
			->setSkeleton($this->skeleton)
			->setExtractor(new Extractor());
	}
	
	/**
	 * @param ConstructorConnector $connector
	 * @param string $type
	 * @return mixed
	 */
	private function invokeConnect(ConstructorConnector $connector, $type)
	{
		return $connector->connect(new \ReflectionClass($type));
	}
	
	/**
	 * @param mixed $value
	 */
	private function setSkeletonToReturn($value)
	{
		if (is_string($value)) 
			$value = new $value;
		
		$this->skeleton->method('get')->willReturn($value);
	}
	
	
	public function test_setSkeleton_ReturnSelf()
	{
		/** @var ISkeletonSource $skeleton */
		$skeleton = $this->getMock(ISkeletonSource::class);
		$obj = new ConstructorConnector();
		
		$this->assertSame($obj, $obj->setSkeleton($skeleton));
	}
	
	
	public function test_setExtractor_ReturnSelf()
	{
		$obj = new ConstructorConnector();
		$this->assertSame($obj, $obj->setExtractor(new Extractor()));
	}
	
	
	public function test_connect_NoConstructor_NewInstanceCreated()
	{
		$obj = $this->getConstructorConnector();
		
		$this->assertInstanceOf(
			test_ConstructorConnector_Helper_EmptyClass::class,
			$this->invokeConnect($obj, test_ConstructorConnector_Helper_EmptyClass::class));
	}
	
	public function test_connect_EmptyConstructor_NewInstanceCreated()
	{
		$obj = $this->getConstructorConnector();
		
		$this->assertInstanceOf(
			test_ConstructorConnector_Helper_EmptyConstructor::class,
			$this->invokeConnect($obj, test_ConstructorConnector_Helper_EmptyConstructor::class));
	}
	
	/**
	 * @expectedException \Exception
	 */
	public function test_connect_ConstructorWithInvalidParameter_ErrorThrown()
	{
		$obj = $this->getConstructorConnector();
		$this->invokeConnect($obj, test_ConstructorConnector_Helper_InvalidType::class);
	}
	
	public function test_connect_ConstructorWithParameters_ParameterClassNamePassedToSkeleton()
	{
		$obj = $this->getConstructorConnector();
		
		$this->skeleton
			->expects($this->once())
			->method('get')
			->with(test_ConstructorConnector_TypeA::class)
			->willReturn(new test_ConstructorConnector_TypeA());
		
		$this->invokeConnect($obj, test_ConstructorConnector_Helper_ConstructorWithParam::class);
	}
	
	public function test_connect_ConstructorWithParameters_InstanceReturned()
	{
		$obj = $this->getConstructorConnector();
		
		$this->setSkeletonToReturn(test_ConstructorConnector_TypeA::class);
		$this->assertInstanceOf(
			test_ConstructorConnector_Helper_ConstructorWithParam::class,
			$this->invokeConnect($obj, test_ConstructorConnector_Helper_ConstructorWithParam::class));
	}
	
	public function test_connect_ConstructorWithParameters_CorrectParameterPassed()
	{
		$obj = $this->getConstructorConnector();
		
		$this->setSkeletonToReturn(test_ConstructorConnector_TypeA::class);
		$instance = $this->invokeConnect($obj, test_ConstructorConnector_Helper_ConstructorWithParam::class);
		
		$this->assertInstanceOf(test_ConstructorConnector_TypeA::class, $instance->a);
	}
	
	public function test_connect_NumberOfParams()
	{
		$obj = $this->getConstructorConnector();
		
		$this->skeleton->expects($this->at(0))->method('get')->willReturn(new test_ConstructorConnector_TypeA);
		$this->skeleton->expects($this->at(1))->method('get')->willReturn(new test_ConstructorConnector_TypeB);
			
		$instance = $this->invokeConnect($obj, test_ConstructorConnector_Helper_NumberOfParams::class);
		
		$this->assertInstanceOf(test_ConstructorConnector_Helper_NumberOfParams::class, $instance);
		$this->assertInstanceOf(test_ConstructorConnector_TypeA::class, $instance->a);
		$this->assertInstanceOf(test_ConstructorConnector_TypeB::class, $instance->b);
	}
}


class test_ConstructorConnector_TypeA {}
class test_ConstructorConnector_TypeB {}

class test_ConstructorConnector_Helper_EmptyClass {}

class test_ConstructorConnector_Helper_EmptyConstructor 
{
	public function __construct() { }
}

class test_ConstructorConnector_Helper_InvalidType
{
	public function __construct($a) { }
}

class test_ConstructorConnector_Helper_ConstructorWithParam
{
	public $a;
	public function __construct(test_ConstructorConnector_TypeA $a) { $this->a = $a; }
}

class test_ConstructorConnector_Helper_NumberOfParams
{
	public $b;
	
	public function __construct(
		test_ConstructorConnector_TypeA $a,
		test_ConstructorConnector_TypeB $b) 
	{ 
		$this->a = $a; 
		$this->b = $b; 
	}
}