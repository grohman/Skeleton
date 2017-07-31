<?php
namespace Skeleton;


use Skeleton\Maps\TestMap;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IContextReference;


class UnitTestSkeleton implements ISkeletonSource
{
	/** @var TestMap */
	private $testMap;
	
	
	private function asContext(?IContextReference $parent): IContextReference
	{
		$context = new Context('unit_test_context', ($parent ? $parent->context() : null));
		$ref = new ContextReference($context, $this);
		
		$context->set($this->testMap->asArray());
			
		return $ref;
	}
	
	
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
		return $this->testMap->get($key, $this->asContext($context));
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