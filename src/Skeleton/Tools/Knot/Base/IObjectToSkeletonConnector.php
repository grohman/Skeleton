<?php
namespace Skeleton\Tools\Knot\Base;


use Skeleton\Base\ISkeletonSource;


interface IObjectToSkeletonConnector
{
	public function setSkeleton(ISkeletonSource $skeleton): IObjectToSkeletonConnector;
}