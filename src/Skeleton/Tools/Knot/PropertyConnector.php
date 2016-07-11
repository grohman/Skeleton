<?php
namespace Skeleton\Tools\Knot;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Annotation\Extractor;


class PropertyConnector
{
	/** @var ISkeletonSource */
	private $skeleton;
	
	/** @var Extractor */
	private $extractor;
	
	
	/**
	 * @param \ReflectionProperty $property
	 * @param string $type
	 * @return string
	 */
	private function getFullTypeName(\ReflectionProperty $property, $type)
	{
		$namespace = $property->getDeclaringClass()->getNamespaceName();
		
		if (!$namespace)
		{
			return $type;
		}
		
		$namespaceSeparatorPosition = strpos($type, '\\');
		
		if ($namespaceSeparatorPosition === 0)
		{
			return substr($type, 1);
		}
		else if ($namespaceSeparatorPosition !== false)
		{
			$type = explode('\\', $type, 2)[1];
			
		}
		
		return "$namespace\\$type";
	}
	
	/**
	 * @param \ReflectionProperty $property
	 * @param mixed $instance
	 * @return bool
	 */
	private function isPropertyMustBeLoaded(\ReflectionProperty $property, $instance)
	{
		if (!$this->extractor->has($property, KnotConsts::AUTOLOAD_ANNOTATIONS))
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
		
		$type = $this->getFullTypeName($property, $type);
		$value = $this->skeleton->get($type);
		$property->setValue($instance, $value);
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
		foreach ($class->getProperties() as $property)
		{
			if ($this->isPropertyMustBeLoaded($property, $instance))
			{
				$this->loadProperty($property, $instance);
			}
		}
	}
}