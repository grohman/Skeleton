<?php
namespace Skeleton\Tools\Knot\Connectors;


use Skeleton\Base\IContextReference;
use Skeleton\Tools\Knot\KnotConsts;
use Skeleton\Tools\Knot\Base\AbstractObjectToSkeletonConnector;
use Skeleton\Tools\Annotation\Extractor;
use Skeleton\Exceptions\MissingContextException;


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
	 * @return mixed
	 */
	private function getAutoloadValue(\ReflectionProperty $property)
	{
		$type = Extractor::get($property, KnotConsts::VARIABLE_DECLARATION_ANNOTATION);
		
		if (!$type)
			throw new \Exception("Variable autoload is configured but missing it's type: {$property->name}");
		
		$type = $this->getFullTypeName($property, $type);
		
		return $this->get($type);
	}

	/**
	 * @param \ReflectionProperty $property
	 * @param IContextReference $context
	 * @return mixed
	 */
	private function getContextValue(\ReflectionProperty $property, IContextReference $context)
	{
		$name = Extractor::get($property, KnotConsts::CONTEXT_ANNOTATION);
		
		if (!$name)
		{
			$name = Extractor::get($property, KnotConsts::VARIABLE_DECLARATION_ANNOTATION);
			$name = ($name ? $this->getFullTypeName($property, $name) : $property->name);
		}
		
		return $context->value($name);
	}
	
	
	/**
	 * @param \ReflectionClass $class
	 * @param mixed $instance
	 * @param IContextReference|null $context
	 */
	public function connect(\ReflectionClass $class, $instance, ?IContextReference $context = null)
	{
		foreach ($class->getProperties() as $property)
		{
			if ($property->class != $class->name) continue;
			
			if (Extractor::has($property, KnotConsts::AUTOLOAD_ANNOTATIONS))
			{
				$value = $this->getAutoloadValue($property);
			}
			else if (Extractor::has($property, KnotConsts::CONTEXT_ANNOTATION))
			{
				if (is_null($context))
					throw new MissingContextException($class->name);
				
				$value = $this->getContextValue($property, $context);
			}
			else
			{
				continue;
			}
			
			$property->setAccessible(true);
			$property->setValue($instance, $value);
		}
	}
}