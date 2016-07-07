<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Annotation\Extractor;


class Knot
{
	/** @var MethodConnector */
	private $methodConnector;
	
	/** @var ConstructorConnector */
	private $constructorConnector;
	
	/** @var PropertyConnector */
	private $propertyConnector;
	
	/** @var Extractor */
	private $extractor;
	
	
	public function __construct() 
	{
		$this->extractor = new Extractor();
		
		$this->constructorConnector	= new ConstructorConnector();
		$this->methodConnector 		= new MethodConnector();
		$this->propertyConnector	= new PropertyConnector();
		
		$this->constructorConnector->setExtractor($this->extractor);
		$this->propertyConnector->setExtractor($this->extractor);
		$this->methodConnector->setExtractor($this->extractor);
	}
	
	
	/**
	 * @param ISkeletonSource $skeleton
	 * @return static
	 */
	public function setSkeletonSource(ISkeletonSource $skeleton)
	{
		$this->constructorConnector->setSkeleton($skeleton);
		$this->propertyConnector->setSkeleton($skeleton);
		$this->methodConnector->setSkeleton($skeleton);
		return $this;
	}
	
	
	/**
	 * @param string $className
	 * @return bool|mixed False if no auto loading required.
	 */
	public function load($className)
	{
		$reflection = new \ReflectionClass($className);
		
		if (!$this->extractor->has($reflection, KnotConsts::AUTOLOAD_ANNOTATION))
		{
			return $reflection->newInstance();
		}
		
		$instance = $this->constructorConnector->connect($reflection);
		$this->propertyConnector->connect($reflection, $instance);
		$this->methodConnector->connect($reflection, $instance);
		
		return $instance;
	}
}