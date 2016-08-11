<?php
namespace Skeleton\Loader;


use Skeleton\Base\ILoader;
use Skeleton\Tools\Knot\Knot;


class Loader implements ILoader 
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
	 * @param mixed $item
	 * @return mixed
	 */
	public function get($item) 
	{
		if (is_string($item))
		{
			if ($this->knot)
			{
				return $this->knot->load($item);
			}
			else
			{
				return new $item;
			}
		}
		else if (is_callable($item))
		{
			return $item();
		}
		else
		{
			return $this->knot->loadInstance($item);
		}
	}
}