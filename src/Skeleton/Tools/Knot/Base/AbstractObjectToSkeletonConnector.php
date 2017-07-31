<?php
namespace Skeleton\Tools\Knot\Base;


use Skeleton\Base\ISkeletonSource;


class AbstractObjectToSkeletonConnector implements IObjectToSkeletonConnector
{
	/** @var ISkeletonSource */
	private $skeleton;
	
	
	public function setSkeleton(ISkeletonSource $skeleton): IObjectToSkeletonConnector
	{
		$this->skeleton = $skeleton;
		return $this;
	}
	
	
	/**
	 * @param string $target
	 * @return mixed
	 */
	public function get(string $target)
	{
		return $this->skeleton->get($target);
	}
}