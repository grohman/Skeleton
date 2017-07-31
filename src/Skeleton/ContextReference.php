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
	
	
	public function __construct(Context $context, ISkeletonSource $skeleton)
	{
		$this->context = $context;
		$this->skeleton = $skeleton;
	}
	
	public function get(string $key)
	{
		return $this->skeleton->get($key, $this);
	}
	
	public function load(string $key)
	{
		return $this->skeleton->load($key, $this);
	}
	
	public function value(string $key)
	{
		return $this->context->get($key);
	}
	
	public function context(): IContextSource
	{
		return $this->context;
	}
	
	
	public function __debugInfo()
	{
		return [ 'context' => $this->context->name() ];
	}
}