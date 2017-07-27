<?php
namespace Skeleton\Tools\Knot\Connectors;


use Skeleton\Tools\Knot\KnotConsts;
use Skeleton\Tools\Knot\Base\AbstractObjectToSkeletonConnector;
use Skeleton\Tools\Annotation\Extractor;


class PropertyConnector extends AbstractObjectToSkeletonConnector
{
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
			$type = substr($type, 1);
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
		if (!Extractor::has($property, KnotConsts::AUTOLOAD_ANNOTATIONS))
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
		$type = Extractor::get($property, KnotConsts::VARIABLE_DECLARATION_ANNOTATION);
		
		if (!$type)
		{
			throw new \Exception('Variable autoload is configured but missing it\'s type: ' . $property->name);
		}
		
		$type = $this->getFullTypeName($property, $type);
		$value = $this->get($type);
		$property->setValue($instance, $value);
	}
	
	
	/**
	 * @param \ReflectionClass $class
	 * @param mixed $instance
	 */
	public function connect(\ReflectionClass $class, $instance)
	{
		foreach ($class->getProperties() as $property)
		{
			if ($property->class == $class->name && 
				$this->isPropertyMustBeLoaded($property, $instance))
			{
				$this->loadProperty($property, $instance);
			}
		}
	}
}