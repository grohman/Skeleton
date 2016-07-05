<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Skeleton;
use Skeleton\Tools\Annotation\Extractor;


class ConstructorConnector
{
	/** @var Skeleton */
	private $skeleton;
	
	/** @var Extractor */
	private $extractor;
	
	
	/**
	 * @param \ReflectionMethod $method
	 * @return bool
	 */
	private function isAutoloadMethod(\ReflectionMethod $method)
	{
		if (strpos($method->getName(), KnotConsts::AUTOLOAD_METHOD_PREFIX) !== 0 || 
			$method->getNumberOfParameters() != 1 || 
			$method->getNumberOfRequiredParameters() != 1 ||
			$method->isStatic() || 
			$method->isAbstract() || 
			!$this->extractor->has($method, KnotConsts::AUTOLOAD_ANNOTATION))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 */
	private function loadParameter(\ReflectionParameter $parameter)
	{
		$className = $parameter->getClass();
		
		if (is_null($className))
		{
			throw new \Exception(
				'Constructor parameter must be autoloaded but missing parameter type for parameter ' . 
					$parameter->getName());
		}
		
		return $this->skeleton->get($className);
	}
	
	
	/**
	 * @param Skeleton $skeleton
	 * @return static
	 */
	public function setSkeleton(Skeleton $skeleton)
	{
		$this->skeleton = $skeleton;
		return $this;
	}
	
	/**
	 * @param Extractor $extractor
	 * @return static
	 */
	public function setExtractor(Extractor $extractor)
	{
		$this->extractor = $extractor;
		return $this;
	}
	
	
	/**
	 * @param \ReflectionClass $class
	 * @return mixed
	 */
	public function connect(\ReflectionClass $class)
	{
		$values = [];
		$constructor = $class->getConstructor();
		
		if (is_null($constructor) || $constructor->getNumberOfRequiredParameters() == 0)
			return $class->newInstance();
		
		foreach ($constructor->getParameters() as $parameter)
		{
			$values[] = $this->loadParameter($parameter);
		}
		
		return $class->newInstanceArgs($values);
	}
}