<?php
use Skeleton\Skeleton;


$skeleton = new Skeleton();
$skeleton->enableKnot();


$skeleton->set(PropertyHelperA::class, PropertyHelperA::class);


class PropertyHelperA
{
	
}


/**
 * @autoload
 */
class PropertyHelperB
{
	/**
	 * @autoload
	 * @var \PropertyHelperA
	 */
	public $field;
}


return $skeleton->load(new PropertyHelperB());