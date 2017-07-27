<?php
namespace Skeleton\Tools\Knot\Base;


use Skeleton\Base\ISkeletonSource;


interface IObjectToSkeletonConnector
{
	/**
	 * @param ISkeletonSource $skeleton
	 * @return static|IObjectToSkeletonConnector
	 */
	public function setSkeleton(ISkeletonSource $skeleton): IObjectToSkeletonConnector;
}