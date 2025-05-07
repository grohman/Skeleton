<?php
namespace Skeleton\Tools\Knot\Connectors;


use Skeleton\Tools\Knot\Base\AbstractObjectToSkeletonConnector;


class ConstructorConnector extends AbstractObjectToSkeletonConnector
{
	/**
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 */
	private function loadParameter(\ReflectionParameter $parameter)
	{
		$class = get_param_class($parameter);
		
		if (is_null($class))
		{
			throw new \Exception(
				'Constructor parameter must be autoloaded but missing parameter type for parameter ' . 
					$parameter->getName());
		}
		
		return $this->get($class->getName());
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
		{
			return $class->newInstance();
		}
		
		foreach ($constructor->getParameters() as $parameter)
		{
			$values[] = $this->loadParameter($parameter);
		}
		
		return $class->newInstanceArgs($values);
	}
}