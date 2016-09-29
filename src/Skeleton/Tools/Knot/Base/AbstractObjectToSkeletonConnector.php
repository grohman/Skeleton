<?php
namespace Skeleton\Tools\Knot\Base;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Annotation\Extractor;


class AbstractObjectToSkeletonConnector implements IObjectToSkeletonConnector
{
	/** @var ISkeletonSource */
	private $skeleton;
	
	/** @var Extractor */
	private $extractor;
	
	
	/**
	 * @return ISkeletonSource
	 */
	protected function getSkeleton()
	{
		return $this->skeleton;
	}
	
	/**
	 * @return Extractor
	 */
	protected function getAnnotationsExtractor()
	{
		return $this->extractor;
	}
	
	
	/**
	 * @param ISkeletonSource $skeleton
	 * @return static
	 */
	public function setSkeleton(ISkeletonSource $skeleton)
	{
		$this->skeleton = $skeleton;
		return $this;
	}
	
	/**
	 * @param Extractor $extractor
	 * @return static
	 */
	public function setExtractor(Extractor $extractor)
	{
		$this->extractor = $extractor;
		return $this;
	}
}