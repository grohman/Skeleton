<?php
namespace Skeleton\Base;


use Skeleton\Tools\Knot\Knot;


interface ILoader
{
	/**
	 * @param Knot $knot
	 */
	public function setKnot(Knot $knot);

	/**
	 * @param mixed $item
	 * @param IContextReference|null $context
	 * @return mixed
	 */
	public function get($item, ?IContextReference $context = null);
}