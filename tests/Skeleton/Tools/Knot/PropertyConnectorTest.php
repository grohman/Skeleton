<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Skeleton;
use Skeleton\Tools\Annotation\Extractor;


class PropertyConnectorTest extends \PHPUnit_Framework_TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|Skeleton */
	private $skeleton;
	
	
	/**
	 * @return PropertyConnector
	 */
	private function getPropertyConnector()
	{
		$this->skeleton = $this->getMock(Skeleton::class);
		return (new PropertyConnector())
			->setSkeleton($this->skeleton)
			->setExtractor(new Extractor());
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
	 * @param PropertyConnector $connector
	 * @param string $type
	 * @return mixed
	 */
	private function invokeConnect(PropertyConnector $connector, $type)
	{
		$instance = new $type;
		$connector->connect(new \ReflectionClass($type), $instance);
		return $instance;
	}
	
	
	public function test_setSkeleton_ReturnSelf()
	{
		/** @var Skeleton $skeleton */
		$skeleton = $this->getMock(Skeleton::class);
		$obj = new PropertyConnector();
		
		$this->assertSame($obj, $obj->setSkeleton($skeleton));
	}
	
	
	public function test_setExtractor_ReturnSelf()
	{
		$obj = new PropertyConnector();		
		$this->assertSame($obj, $obj->setExtractor(new Extractor()));
	}
	
	
	public function test_connect_EmptyClass_SkeletonNotCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonNotCalled();
		$this->invokeConnect($obj, test_PropertyConnector_Helper_EmptyClass::class);
	}
	
	public function test_connect_NoAutoloadParams_SkeletonNotCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonNotCalled();
		$this->invokeConnect($obj, test_PropertyConnector_Helper_NoAutoload::class);
	}
	
	public function test_connect_PublicAutoloadParameter_SkeletonCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonCalledFor('PubType', 1);
		
		$instance = $this->invokeConnect($obj, test_PropertyConnector_Helper_PublicAutoload::class);
		$this->assertEquals(1, $instance->pub);
	}
	
	public function test_connect_ProtectedAutoloadParameter_SkeletonCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonCalledFor('ProtType', 2);
		$instance = $this->invokeConnect($obj, test_PropertyConnector_Helper_ProtectedAutoload::class);
		$this->assertEquals(2, $instance->get());
	}
	
	public function test_connect_PrivTypeAutoloadParameter_SkeletonCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonCalledFor('PrivType', 3);
		$instance = $this->invokeConnect($obj, test_PropertyConnector_Helper_PrivateAutoload::class);
		$this->assertEquals(3, $instance->get());
	}
	
	public function test_connect_NumberOfProperties_AllLoaded()
	{
		$obj = $this->getPropertyConnector();
		$this->setSkeletonToReturn('value');
		$instance = $this->invokeConnect($obj, test_PropertyConnector_Helper_NumberOfProperties::class);
		
		$this->assertEquals('value', $instance->pub);
		$this->assertEquals('value', $instance->get());
	}
	
	/**
	 * @expectedException \Exception
	 */
	public function test_connect_PropertyHasNoType_ErrorThrown()
	{
		$obj = $this->getPropertyConnector();
		$this->invokeConnect($obj, test_PropertyConnector_Helper_NoType::class);
	}
}


class test_PropertyConnector_Helper_EmptyClass {}

class test_PropertyConnector_Helper_NoAutoload 
{
	private		$a;
	protected	$b;
	public		$c;
}

class test_PropertyConnector_Helper_PublicAutoload
{
	/**
	 * @autoload
	 * @var PubType
	 */
	public $pub;
}

class test_PropertyConnector_Helper_ProtectedAutoload
{
	/**
	 * @autoload
	 * @var ProtType
	 */
	protected $prot;
	
	public function get() { return $this->prot; }
}

class test_PropertyConnector_Helper_PrivateAutoload
{
	/**
	 * @autoload
	 * @var PrivType
	 */
	private	$priv;
	
	public function get() { return $this->priv; }
}

class test_PropertyConnector_Helper_NumberOfProperties
{
	/**
	 * @autoload
	 * @var PrivType
	 */
	private $priv;
	
	/**
	 * @autoload
	 * @var PubType
	 */
	public $pub;
	
	public function get() { return $this->priv; }
}

class test_PropertyConnector_Helper_NoType
{
	/**
	 * @autoload
	 */
	private $noType;
}