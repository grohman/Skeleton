<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Annotation\Extractor;
use Skeleton\Tools\Knot\Connectors\MethodConnector;
use Skeleton\Tools\Knot\Connectors\PropertyConnector;
use Skeleton\Tools\Knot\Connectors\ConstructorConnector;


class Knot
{
	/** @var MethodConnector */
	private $methodConnector;
	
	/** @var ConstructorConnector */
	private $constructorConnector;
	
	/** @var PropertyConnector */
	private $propertyConnector;
	
	
	/**
	 * @param \ReflectionClass $reflection
	 * @return bool
	 */
	private function isAutoloadClass(\ReflectionClass $reflection)
	{
		return Extractor::has($reflection, KnotConsts::AUTOLOAD_ANNOTATIONS);
	}
	
	
	public function __construct(ISkeletonSource $skeleton) 
	{
		$this->constructorConnector	= new ConstructorConnector();
		$this->methodConnector 		= new MethodConnector();
		$this->propertyConnector	= new PropertyConnector();
		
		$this->constructorConnector->setSkeleton($skeleton);
		$this->propertyConnector->setSkeleton($skeleton);
		$this->methodConnector->setSkeleton($skeleton);
	}
	
	
	/**
	 * @param mixed $instance
	 * @return mixed Same instance always returned
	 */
	public function loadInstance($instance)
	{
		$reflection = new \ReflectionClass($instance);
		
		while ($reflection)
		{
			if ($this->isAutoloadClass($reflection))
			{
				$this->propertyConnector->connect($reflection, $instance);
				$this->methodConnector->connect($reflection, $instance);
			}
			
			$reflection = $reflection->getParentClass();
		}
		
		return $instance;
	}
	
	/**
	 * @param string $className
	 * @return bool|mixed False if no auto loading required.
	 */
	public function load($className)
	{
		$reflection = new \ReflectionClass($className);
		$instance = $this->constructorConnector->connect($reflection);
		
		return $this->loadInstance($instance);
	}
}