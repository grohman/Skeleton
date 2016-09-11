<?php
namespace Skeleton\Maps;


use Skeleton\Base\IMap;
use Skeleton\Base\ILoader;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Knot\Knot;
use Skeleton\Loader\Loader;


abstract class BaseMap implements IMap
{
	private $loader;
	
	
	public function __construct(ILoader $loader = null) 
	{
		$this->loader = ($loader ?: new Loader());
	}
	
	
	/**
	 * @return ILoader
	 */
	public function loader()
	{
		return $this->loader;
	}
	
	/**
	 * @param ILoader $loader
	 */
	public function setLoader(ILoader $loader)
	{
		$this->loader = $loader;
	}
	
	/**
	 * @param ISkeletonSource $skeletonSource
	 */
	public function enableKnot(ISkeletonSource $skeletonSource)
	{
		$this->loader()->setKnot(new Knot($skeletonSource));
	}
}