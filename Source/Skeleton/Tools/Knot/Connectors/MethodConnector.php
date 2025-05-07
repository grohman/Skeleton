<?php
namespace Skeleton\Tools\Knot\Connectors;


use Skeleton\Base\IContextReference;
use Skeleton\Tools\Knot\KnotConsts;
use Skeleton\Tools\Knot\Base\AbstractObjectToSkeletonConnector;
use Skeleton\Tools\Annotation\Extractor;
use Skeleton\Exceptions\MissingContextException;


class MethodConnector extends AbstractObjectToSkeletonConnector
{
	/**
	 * @param \ReflectionMethod $method
	 * @return bool
	 */
	private function isAutoloadMethod(\ReflectionMethod $method)
	{
		return (strpos($method->getName(), KnotConsts::AUTOLOAD_METHOD_PREFIX) === 0 && 
			$method->getNumberOfParameters() == 1 &&
			$method->getNumberOfRequiredParameters() == 1 &&
			!$method->isStatic() && 
			!$method->isAbstract());
	}

	/**
	 * @param \ReflectionMethod $method
	 * @return mixed
	 */
	private function getAutoloadValue(\ReflectionMethod $method)
	{
		$class = get_param_class($method);
		
		if (is_null($class))
		{
			throw new \Exception('Method autoload is configured but missing it\'s parameter type: ' . $method->name);
		}
		
		return $this->get($class->getName());
	}

	/**
	 * @param \ReflectionMethod $method
	 * @param IContextReference $context
	 * @return string
	 */
	private function getContextValue(\ReflectionMethod $method, IContextReference $context)
	{
		$name = Extractor::get($method, KnotConsts::CONTEXT_ANNOTATION);
		
		if (!$name)
		{
			$parameter = $method->getParameters()[0];
			$class = get_param_class($method);
			$name = ($class ? $class->getName() : $parameter->getName());
		}
		
		return $context->value($name);
	}
	
	
	/**
	 * @param \ReflectionClass $class
	 * @param IContextReference|null $context
	 * @param mixed $instance
	 */
	public function connect(\ReflectionClass $class, $instance, ?IContextReference $context = null)
	{
		foreach ($class->getMethods() as $method)
		{
			if ($method->class != $class->name || !$this->isAutoloadMethod($method)) continue;
			
			if (Extractor::has($method, KnotConsts::AUTOLOAD_ANNOTATIONS))
			{
				$value = $this->getAutoloadValue($method);
			}
			else if (Extractor::has($method, KnotConsts::CONTEXT_ANNOTATION)) 
			{
				if (is_null($context))
					throw new MissingContextException($class->name);
					
				$value = $this->getContextValue($method, $context);
			}
			else
			{
				continue;
			}
				
			$method->setAccessible(true);
			$method->invoke($instance, $value);
		}
	}
}