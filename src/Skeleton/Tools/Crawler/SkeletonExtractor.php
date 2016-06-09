<?php
namespace Skeleton\Tools\Crawler;


class SkeletonExtractor
{
	const SKELETON_ANNOTATION	= '/^[\/\s\*]*@skeleton[\/\s\*]*$/im';
	const BONE_ANNOTATION		= '/^[\/\s\*]*@bone[\/\s\*]*$/im';
	
	
	private $interfaces		= [];
	private $implementers	= [];
	
	
	/**
	 * @param array $declarations
	 */
	public function extract(array $declarations) 
	{
		foreach ($declarations as $declaration)
		{
			$reflection = new \ReflectionClass($declaration);
			$doc = $reflection->getDocComment();
			
			if (preg_match(self::SKELETON_ANNOTATION, $doc))
			{
				$this->interfaces[] = $declaration;
			}
			else if (preg_match(self::BONE_ANNOTATION, $doc))
			{
				$this->implementers[] = $declaration;
			}
		}
	}
	
	
	/**
	 * @return array
	 */
	public function getInterfaces()
	{
		return $this->interfaces;
	}
	
	/**
	 * @return array
	 */
	public function getImplementers()
	{
		return $this->implementers;
	}
}