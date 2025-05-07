<?php
function skeleton($item)
{
	return \Skeleton\Skeleton::container($item);
}

/**
 * @param \ReflectionMethod|\ReflectionParameter $source
 * @return \ReflectionClass|null
 */
function get_param_class($source): ?\ReflectionClass
{
	if ($source instanceof \ReflectionMethod)
	{
		$parameter = $source->getParameters()[0];
	}
	else if ($source instanceof \ReflectionParameter)
	{
		$parameter = $source;
	}
	else
	{
		throw new \Exception("Get param class from unsupported source");
	}
	
	$type = $parameter->getType();
	
	if (!($type instanceof \ReflectionNamedType))
		return null;
	
	if (\Skeleton\BuiltInType::isConstValueExists($type->getName()))
		return null;
	
	return new \ReflectionClass($type->getName());
}


if (!function_exists('each'))
{
	/**
	 * @param array $array Although the actual function accepted objects, it was discouraged to pass them. Hence for the
	 *                     shim only an array is supported.
	 *
	 * @return array|false
	 */
	function each(array &$array)
	{
		$key   = key($array);
		$value = current($array);
		
		if (is_null($key))
		{
			// key() returns null if the array pointer is beyond the list of element or if the array is empty. If the
			// same scenario occurred in the each() function, a false was returned instead. Hence returning false here
			return false;
		}
		
		// Advance the array pointer before returning
		next($array);
		
		return [
			1       => $value,
			'value' => $value,
			0       => $key,
			'key'   => $key
		];
	}
}