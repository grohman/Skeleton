<?php
namespace Skeleton;


use Skeleton\Maps\TestMap;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IContextReference;
use Skeleton\Tools\ContextManager;


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
	 * @param IContextReference|Context|array|null $context
	 * @param bool $skipGlobal
	 * @return mixed
	 */
	public function get($key, $context = null, bool $skipGlobal = false)
	{
		if ($context && !$context instanceof IContextReference)
			$context = ContextManager::create($this, $context);
		
		return $this->testMap->get($key, $this->asContext($context));
	}

	/**
	 * @param string|mixed $item
	 * @param IContextReference|Context|array|null $context
	 * @return mixed
	 */
	public function load($item, $context = null)
	{
		if ($context && !$context instanceof IContextReference)
			$context = ContextManager::create($this, $context);
		
		return $this->testMap->loader()->get($item, $context);
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