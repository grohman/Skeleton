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
	 * @param mixed $className
	 * @return mixed
	 */
	public function get($className);
}