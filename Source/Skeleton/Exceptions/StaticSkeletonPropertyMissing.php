<?php
namespace Skeleton\Exceptions;


use Skeleton\AbstractSkeleton;


class StaticSkeletonPropertyMissing extends \Error
{
	public function __construct()
	{
		parent::__construct(
			'The definition "protected static $skeleton;" must be present in a class extending ' .
			AbstractSkeleton::class);
	}
}