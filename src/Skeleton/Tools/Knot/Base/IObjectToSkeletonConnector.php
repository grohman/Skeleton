<?php
namespace Skeleton\Tools\Knot\Base;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Annotation\Extractor;


interface IObjectToSkeletonConnector
{
	/**
	 * @param ISkeletonSource $skeleton
	 * @return static
	 */
	public function setSkeleton(ISkeletonSource $skeleton);
	
	/**
	 * @param Extractor $extractor
	 * @return static
	 */
	public function setExtractor(Extractor $extractor);
}