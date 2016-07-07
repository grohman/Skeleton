<?php
namespace Skeleton\Maps;


use Skeleton\Base\ILoader;
use Skeleton\Tools\Knot\Knot;


class LazyLoader implements ILoader 
{
	/** @var Knot|null */
	private $knot = null;
	
	
	/**
	 * @param Knot $knot
	 */
	public function setKnot(Knot $knot)
	{
		$this->knot = $knot;
	}
	
	/**
	 * @param mixed $className
	 * @return mixed
	 */
	public function get($className) 
	{
		if (is_string($className))
		{
			if ($this->knot)
			{
				return $this->knot->load($className);
			}
			else
			{
				return new $className;
			}
		}
		else if (is_callable($className))
		{
			return $className();
		}
		else
		{
			return $className;
		}
	}
}