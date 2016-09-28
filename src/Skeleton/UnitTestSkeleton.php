<?php
namespace Skeleton;


use Skeleton\Maps\TestMap;
use Skeleton\Base\AbstractSkeletonSource;


class UnitTestSkeleton extends AbstractSkeletonSource
{
	/** @var TestMap */
	private $testMap;
	
	
	/**
	 * @param Skeleton $skeleton
	 */
	public function __construct(Skeleton $skeleton) 
	{
		$this->testMap = new TestMap($skeleton->getMap());
		$skeleton->setMap($this->testMap);
	}
	
	
	/**
	 * @param string $key
	 * @param bool $useGlobal
	 * @return mixed
	 */
	public function get($key, $useGlobal = true)
	{
		return $this->testMap->get($key);
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return static
	 */
	public function override($key, $value)
	{
		$this->testMap->override($key, $value);
		return $this;
	}
	
	public function clear()
	{
		$this->testMap->clear();
	}
}