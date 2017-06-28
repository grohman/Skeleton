<?php
namespace Skeleton;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Exceptions\ImplementerNotDefinedException;


class GlobalSkeleton
{
	use \Objection\TSingleton;
	
	
	/** @var ISkeletonSource[][] */
	private $skeletons = [];
	
	
	/**
	 * @param string $key
	 * @return ISkeletonSource|null
	 */
	public function getSkeleton($key)
	{
		$length		= strlen($key);
		$keyStart	= substr($key, 0, 3);
		
		if (!isset($this->skeletons[$keyStart]))
			return null;
		
		foreach ($this->skeletons[$keyStart] as $prefix => $skeletonSource)
		{
			$prefixLength = strlen($prefix);
			
			if ($prefixLength > $length || substr($key, 0, $prefixLength) != $prefix)
				continue;
			
			return $skeletonSource;
		}
		
		return null;
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		$source = $this->getSkeleton($key) ?? FindSkeleton::getSkeleton($key);
		
		if (!$source)
			throw new ImplementerNotDefinedException($key);
		
		return $source->getLocal($key);
	}
	
	/**
	 * @param string $namespacePrefix
	 * @param ISkeletonSource $skeletonSource
	 */
	public function add($namespacePrefix, ISkeletonSource $skeletonSource)
	{
		$prefixStart = substr($namespacePrefix, 0, 3);
		
		if (!isset($this->skeletons[$prefixStart]))
		{
			$this->skeletons[$prefixStart] = [];
		}
		
		$this->skeletons[$prefixStart][$namespacePrefix] = $skeletonSource;
	}
}