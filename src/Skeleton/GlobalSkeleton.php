<?php
namespace Skeleton;


use Skeleton\Base\ISkeletonSource;


class GlobalSkeleton
{
	use \Objection\TSingleton;
	
	
	/** @var ISkeletonSource[][] */
	private $skeletons = [];
	
	
	/**
	 * @param string $key
	 * @return ISkeletonSource|null
	 */
	public function get($key)
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