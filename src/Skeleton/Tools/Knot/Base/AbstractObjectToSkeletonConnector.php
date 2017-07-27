<?php
namespace Skeleton\Tools\Knot\Base;


use Skeleton\Context;
use Skeleton\Base\ISkeletonSource;


class AbstractObjectToSkeletonConnector implements IObjectToSkeletonConnector
{
	/** @var ISkeletonSource */
	private $skeleton;
	

	/**
	 * @param string $key
	 * @param Context|null $context
	 * @return mixed|null
	 */
	protected function context(string $key, ?Context $context)
	{
		return ($context ? $context->get($key) : null);
	}
	
	protected function hasContextFor(string $key, ?Context $context)
	{
		return ($context ? $context->has($key) : false);
	}
	
	
	public function setSkeleton(ISkeletonSource $skeleton): IObjectToSkeletonConnector
	{
		$this->skeleton = $skeleton;
		return $this;
	}
	
	
	/**
	 * @param string $target
	 * @return mixed
	 */
	public function get(string $target)
	{
		return $this->skeleton->get($target);
	}
}