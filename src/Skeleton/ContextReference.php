<?php
namespace Skeleton;


use Skeleton\Base\IContextSource;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IContextReference;


class ContextReference implements IContextReference
{
	/** @var Context */
	private $context;
	
	/** @var ISkeletonSource */
	private $skeleton;
	
	
	public function get(string $key)
	{
		return $this->skeleton->get($key, $this);
	}
	
	public function context(): IContextSource
	{
		return $this->context;
	}
}