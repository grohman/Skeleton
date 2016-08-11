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
	 * @return mixed
	 */
	public function get($item);
}