<?php
namespace Skeleton\Maps;


use Skeleton\Base\IMap;
use Skeleton\Base\ILoader;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Knot\Knot;
use Skeleton\Loader\ValueLoader;


abstract class BaseMap implements IMap
{
	private $loader;
	
	
	/**
	 * @param ILoader|null $loader
	 */
	public function __construct(ILoader $loader = null) 
	{
		$this->loader = ($loader ?: new ValueLoader());
	}
	
	
	/**
	 * @return ILoader
	 */
	public function loader(): ILoader
	{
		return $this->loader;
	}
	
	/**
	 * @param ILoader $loader
	 */
	public function setLoader(ILoader $loader): void
	{
		$this->loader = $loader;
	}
	
	/**
	 * @param ISkeletonSource $skeletonSource
	 */
	public function enableKnot(ISkeletonSource $skeletonSource): void
	{
		$this->loader()->setKnot(new Knot($skeletonSource));
	}
}