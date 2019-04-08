<?php
namespace Skeleton\Tools\Knot\Base;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IContextReference;


class AbstractObjectToSkeletonConnector implements IObjectToSkeletonConnector
{
	/** @var ISkeletonSource */
	private $skeleton;


	/**
	 * @param string $target
	 * @param IContextReference|null $context
	 * @return mixed
	 */
	protected function get(string $target, ?IContextReference $context = null)
	{
		return $this->skeleton->get($target, $context);
	}


	public function setSkeleton(ISkeletonSource $skeleton): IObjectToSkeletonConnector
	{
		$this->skeleton = $skeleton;
		return $this;
	}
}