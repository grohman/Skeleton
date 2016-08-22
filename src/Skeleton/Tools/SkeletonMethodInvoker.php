<?php
namespace Skeleton\Tools;


use Skeleton\Base\ISkeletonSource;


class SkeletonMethodInvoker
{
	/** @var ISkeletonSource */
	private $source;
	
	
	public function setSkeletonSource(ISkeletonSource $source)
	{
		$this->source = $source;
	}
	
	
	public function invoke($method)
	{
		$params = [];
		
		$reflection = null;
		
		if ($method instanceof \ReflectionFunctionAbstract)
		{
			$reflection = $method;
		}
		else if (is_array($method))
		{
			$reflection = new \ReflectionMethod($method[0], $method[1]);
		}
		else
		{
			$reflection = new \ReflectionFunction($method);
		}
		
		foreach ($reflection->getParameters() as $parameter)
		{
			$params[] = $this->source->get($parameter->getClass()->getName());
		}
	
	
		if ($reflection instanceof \ReflectionMethod)
		{
			/** @var \ReflectionMethod $reflection */
			$reflection->setAccessible(true);
			return $reflection->invoke($method[0], ...$params);
		}
		else
		{
			/** @var \ReflectionFunction $reflection */
			return $reflection->invokeArgs($params);
		}
	}
}