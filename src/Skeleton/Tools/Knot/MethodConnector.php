<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Annotation\Extractor;


class MethodConnector
{
	/** @var ISkeletonSource */
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
			!$this->extractor->has($method, KnotConsts::AUTOLOAD_ANNOTATIONS))
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
		$class = $parameter->getClass();
		
		if (is_null($class))
		{
			throw new \Exception('Method autoload is configured but missing it\'s parameter type: ' . $method->name);
		}
		
		$className = $class->getName();
		$method->setAccessible(true);
		$value = $this->skeleton->get($className);
		$method->invoke($instance, $value);
	}
	
	
	/**
	 * @param ISkeletonSource $skeleton
	 * @return static
	 */
	public function setSkeleton(ISkeletonSource $skeleton)
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
			if ($method->class == $class->name && 
				$this->isAutoloadMethod($method))
			{
				$this->invokeMethod($method, $instance);
			}
		}
	}
}