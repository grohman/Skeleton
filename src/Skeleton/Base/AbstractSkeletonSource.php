<?php
namespace Skeleton\Base;


abstract class AbstractSkeletonSource implements ISkeletonSource
{
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getLocal($key)
	{
		return $this->get($key, false);
	}
}