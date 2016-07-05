<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Skeleton;
use Skeleton\Tools\Annotation\Extractor;


class MethodConnector
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
	 * @param \ReflectionMethod $method
	 * @param mixed $instance
	 */
	private function invokeMethod(\ReflectionMethod $method, $instance)
	{
		$parameter = $method->getParameters()[0];
		$className = $parameter->getClass();
		
		if (is_null($className))
		{
			throw new \Exception('Method autoload is configured but missing it\'s parameter type: ' . $method->name);
		}
		
		$method->setAccessible(true);
		$method->invoke($instance, $this->skeleton->get($className));
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
	 * @param mixed $instance
	 */
	public function connect(\ReflectionClass $class, $instance)
	{
		foreach ($class->getMethods() as $method)
		{
			if (!$this->isAutoloadMethod($method))
			{
				$this->invokeMethod($method, $instance);
			}
		}
	}
}