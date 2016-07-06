<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Skeleton;
use Skeleton\Tools\Annotation\Extractor;


class PropertyConnector
{
	/** @var Skeleton */
	private $skeleton;
	
	/** @var Extractor */
	private $extractor;
	
	
	/**
	 * @param \ReflectionProperty $property
	 * @param mixed $instance
	 * @return bool
	 */
	private function isPropertyMustBeLoaded(\ReflectionProperty $property, $instance)
	{
		if (!$this->extractor->has($property, KnotConsts::AUTOLOAD_ANNOTATION))
			return false;
		
		$property->setAccessible(true);
		
		return !$property->getValue($instance);
	}
	
	/**
	 * @param \ReflectionProperty $property
	 * @param mixed $instance
	 */
	private function loadProperty(\ReflectionProperty $property, $instance)
	{
		$type = $this->extractor->get($property, KnotConsts::VARIABLE_DECLARATION_ANNOTATION);
		
		if (!$type)
		{
			throw new \Exception('Variable autoload is configured but missing it\'s type: ' . $property->name);
		}
		
		$value = $this->skeleton->get($type);
		$property->setValue($instance, $value);
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
		foreach ($class->getProperties() as $property)
		{
			if ($this->isPropertyMustBeLoaded($property, $instance))
			{
				$this->loadProperty($property, $instance);
			}
		}
	}
}