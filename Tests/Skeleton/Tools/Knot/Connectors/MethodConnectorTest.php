<?php
namespace Skeleton\Tools\Knot\Connectors;


use PHPUnit\Framework\MockObject\MockObject;

use Skeleton\Context;
use Skeleton\ContextReference;
use Skeleton\Base\ISkeletonSource;


class MethodConnectorTest extends \SkeletonTestCase
{
	/** @var MockObject|ISkeletonSource */
	private $skeleton;
	
	
	private function assertContextLoaded(string $item, string $className, $value)
	{
		$obj = $this->getMethodConnector();
		$context = new Context('a');
		$ref = new ContextReference($context, $this->skeleton);
		$inst = new $className();
		
		$context->set($item, $value);
		
		$obj->connect(new \ReflectionClass($inst), $inst, $ref);
		
		
		self::assertEquals($value, $inst->i);
	}
	
	/**
	 * @return MethodConnector
	 */
	private function getMethodConnector()
	{
		$this->skeleton = $this->getMock(ISkeletonSource::class);
		
		/** @var MethodConnector $res */
		$res = (new MethodConnector())->setSkeleton($this->skeleton);
		return $res;
	}
	
	private function expectSkeletonNotCalled()
	{
		$this->skeleton->expects($this->never())->method('get');
	}
	
	/**
	 * @param string $type
	 * @param mixed $return
	 */
	private function expectSkeletonCalledFor($type, $return = null)
	{
		$this->skeleton->expects($this->once())->method('get')->with($type)->willReturn($return);
	}
	
	/**
	 * @param mixed $value
	 */
	private function setSkeletonToReturn($value)
	{
		$this->skeleton->method('get')->willReturn($value);
	}
	
	/**
	 * @param MethodConnector $connector
	 * @param string $type
	 * @return mixed
	 */
	private function invokeConnect(MethodConnector $connector, $type)
	{
		$instance = new $type;
		$connector->connect(new \ReflectionClass($type), $instance);
		return $instance;
	}
	
	/**
	 * @param string $className
	 */
	private function assertMethodCalled($className)
	{
		$obj = $this->getMethodConnector();
		$returnObject = new test_MethodConnector_TypeA();
		$this->expectSkeletonCalledFor(test_MethodConnector_TypeA::class, $returnObject);
		
		$instance = $this->invokeConnect($obj, $className);
		
		$this->assertSame($returnObject, $instance->get());
	}
	
	
	public function test_setSkeleton_ReturnSelf()
	{
		/** @var ISkeletonSource $skeleton */
		$skeleton = $this->getMock(ISkeletonSource::class);
		$obj = new MethodConnector();
		
		$this->assertSame($obj, $obj->setSkeleton($skeleton));
	}
	
	
	public function test_connect_EmptyClass_SkeletonNotCalled()
	{
		$obj = $this->getMethodConnector();
		$this->expectSkeletonNotCalled();
		$this->invokeConnect($obj, test_MethodConnector_Helper_EmptyClass::class);
	}
	
	public function test_connect_NoAutoloadMethod_SkeletonNotCalled()
	{
		$obj = $this->getMethodConnector();
		$this->expectSkeletonNotCalled();
		$this->invokeConnect($obj, test_MethodConnector_Helper_NoAutoload::class);
	}
	
	public function test_connect_MethodIsNotAValidAutoloadMethod_SkeletonNotCalled()
	{
		$obj = $this->getMethodConnector();
		$this->expectSkeletonNotCalled();
		$this->invokeConnect($obj, test_MethodConnector_Helper_InvalidMethod::class);
	}
	
	public function test_connect_PublicAutoloadParameter_SkeletonCalled()
	{
		$this->assertMethodCalled(test_MethodConnector_Helper_PublicAutoload::class);
	}
	
	public function test_connect_ProtectedAutoloadParameter_SkeletonCalled()
	{
		$this->assertMethodCalled(test_MethodConnector_Helper_ProtectedAutoload::class);
	}
	
	public function test_connect_PrivateTypeAutoloadParameter_SkeletonCalled()
	{
		$this->assertMethodCalled(test_MethodConnector_Helper_PrivateAutoload::class);
	}
	
	public function test_connect_NumberOfProperties_AllLoaded()
	{
		$obj = $this->getMethodConnector();
		$returnObject = new test_MethodConnector_TypeA();
		$this->setSkeletonToReturn($returnObject);
		
		$instance = $this->invokeConnect($obj, test_MethodConnector_Helper_NumberOfProperties::class);
		
		$this->assertSame($returnObject, $instance->getA());
		$this->assertSame($returnObject, $instance->getB());
	}
	
	public function test_connect_MethodParameterHasNoType()
	{
		$this->expectException(\Exception::class);
		
		$obj = $this->getMethodConnector();
		$this->invokeConnect($obj, test_MethodConnector_Helper_NoType::class);
	}
	
	
	public function test_connect_ContextMethod_ContextResolvedByAnnotationValue()
	{
		$this->assertContextLoaded('a', test_MethodConnector_Helper_ContextByAnnotation::class, 123);
	}
	
	public function test_connect_ContextMethod_ContextResolvedByParameterName()
	{
		$this->assertContextLoaded('i', test_MethodConnector_Helper_ContextByParamName::class, 123);
	}
	
	public function test_connect_ContextMethod_ContextResolvedByParameterType()
	{
		$this->assertContextLoaded(
			test_MethodConnector_cls::class, 
			test_MethodConnector_Helper_ContextByType::class, 
			new test_MethodConnector_cls());
	}
	
	public function test_connect_ContextNotSet_ExceptionThrown()
	{
		$this->expectException(\Skeleton\Exceptions\MissingContextException::class);
		
		$obj = $this->getMethodConnector();
		$inst = new test_MethodConnector_Helper_ContextByAnnotation();
		
		$obj->connect(new \ReflectionClass($inst), $inst, null);
	}
}


class test_MethodConnector_TypeA {}

class test_MethodConnector_Helper_EmptyClass {}

class test_MethodConnector_Helper_NoAutoload 
{
	public function setSomeVarPublic(test_MethodConnector_TypeA $a) {}
	protected function setSomeVarProtected(test_MethodConnector_TypeA $a) {}
	private function setSomeVarPrivate(test_MethodConnector_TypeA $a) {}
}

class test_MethodConnector_Helper_InvalidMethod
{
	/**
	 * @autoload
	 */
	public function setA(test_MethodConnector_TypeA $a = null) {}
	
	/**
	 * @autoload
	 */
	public function setB(test_MethodConnector_TypeA $a, $b) {}
	
	/**
	 * @autoload
	 */
	public function notSetC(test_MethodConnector_TypeA $a, $b) {}
	
	/**
	 * @autoload
	 */
	public function setD() {}
	
	/**
	 * @autoload
	 */
	public function setE(test_MethodConnector_TypeA $a, $b = 1) {}
}



class test_MethodConnector_Helper_PublicAutoload
{
	private $value;
	
	/**
	 * @autoload
	 */
	public function setParam(test_MethodConnector_TypeA $a) { $this->value = $a; }
	public function get() { return $this->value; }
}

class test_MethodConnector_Helper_ProtectedAutoload
{
	private $value;
	
	/**
	 * @autoload
	 */
	protected function setParam(test_MethodConnector_TypeA $a) { $this->value = $a; }
	public function get() { return $this->value; }
}

class test_MethodConnector_Helper_PrivateAutoload
{
	private $value;
	
	/**
	 * @autoload
	 */
	protected function setParam(test_MethodConnector_TypeA $a) { $this->value = $a; }
	public function get() { return $this->value; }
}

class test_MethodConnector_Helper_NumberOfProperties
{
	private $valueA;
	private $valueB;
	
	/**
	 * @autoload
	 */
	protected function setParam(test_MethodConnector_TypeA $a) { $this->valueA = $a; }
	public function getA() { return $this->valueA; }
	
	/**
	 * @autoload
	 */
	protected function setParamB(test_MethodConnector_TypeA $a) { $this->valueB = $a; }
	public function getB() { return $this->valueB; }
}

class test_MethodConnector_Helper_NoType
{
	/**
	 * @autoload
	 */
	public function setParam($i) {}
}


class test_MethodConnector_Helper_ContextByAnnotation
{
	public $i;
	
	/**
	 * @context a
	 */
	public function setParam($i) { $this->i = $i; }
}

class test_MethodConnector_Helper_ContextByParamName
{
	public $i;
	
	/**
	 * @context
	 */
	public function setParam($i) { $this->i = $i; }
}

class test_MethodConnector_cls {}
class test_MethodConnector_Helper_ContextByType
{
	public $i;
	
	/**
	 * @context
	 */
	public function setParam(test_MethodConnector_cls $i) { $this->i = $i; }
}