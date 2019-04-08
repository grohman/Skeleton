<?php
namespace Skeleton\Loader;


use Skeleton\Base\ILoader;
use Skeleton\Base\IContextReference;
use Skeleton\Tools\Knot\Knot;


class ValueLoader implements ILoader 
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
	 * @param IContextReference|null $context
	 * @return mixed
	 */
	public function get($item, ?IContextReference $context = null) 
	{
		if (is_string($item))
		{
			if ($this->knot)
			{
				return $this->knot->load($item, $context);
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
			return $this->knot->loadInstance($item, $context);
		}
	}
}