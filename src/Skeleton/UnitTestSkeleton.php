<?php
namespace Skeleton;


use Skeleton\Maps\TestMap;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IContextReference;


class UnitTestSkeleton implements ISkeletonSource
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
	 * @param IContextReference|null $context
	 * @param bool $skipGlobal
	 * @return mixed
	 */
	public function get($key, ?IContextReference $context = null, bool $skipGlobal = false)
	{
		return $this->testMap->get($key, $context);
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