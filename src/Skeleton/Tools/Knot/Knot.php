<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IContextReference;
use Skeleton\Tools\ContextManager;
use Skeleton\Tools\Knot\Connectors\MethodConnector;
use Skeleton\Tools\Knot\Connectors\PropertyConnector;
use Skeleton\Tools\Knot\Connectors\ConstructorConnector;
use Skeleton\Tools\Annotation\Extractor;
use Skeleton\Exceptions\MissingContextException;


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
		return Extractor::has($reflection, KnotConsts::AUTOLOAD_ANNOTATIONS) || 
			Extractor::has($reflection, KnotConsts::CONTEXT_ANNOTATION);
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
	 * @param IContextReference|null $context
	 * @return mixed Same instance always returned
	 */
	public function loadInstance($instance, ?IContextReference $context = null)
	{
		$reflection = new \ReflectionClass($instance);
		
		if (Extractor::has($reflection, KnotConsts::CONTEXT_ANNOTATION))
		{
			if (is_null($context))
				throw new MissingContextException($reflection->name);
			
			ContextManager::set($instance, $context);
		}
		
		while ($reflection)
		{
			if ($this->isAutoloadClass($reflection))
			{
				$this->propertyConnector->connect($reflection, $instance, $context);
				$this->methodConnector->connect($reflection, $instance, $context);
			}
			
			$reflection = $reflection->getParentClass();
		}
		
		return $instance;
	}

	/**
	 * @param string $className
	 * @param IContextReference|null $context
	 * @return bool|mixed False if no auto loading required.
	 */
	public function load($className, ?IContextReference $context = null)
	{
		$reflection = new \ReflectionClass($className);
		$instance = $this->constructorConnector->connect($reflection);
		
		return $this->loadInstance($instance, $context);
	}
}