<?php
namespace Skeleton\Tools\Crawler;


class Mapper
{
	private $map;
	private $bones;
	private $invalidBones = [];
	private $invalidSkeletons = [];
	
	
	/**
	 * @param string $className
	 */
	private function validateBone($className)
	{
		$reflection = new \ReflectionClass($className);
		
		if (!$reflection->isInstantiable())
		{
			$this->invalidSkeletons[] = [$className => 'Instance of this class can not be created'];
		}
	}
	
	/**
	 * @param string $className
	 * @return bool True if at least one skeleton class defined for this bone. 
	 */
	private function mapSingleImplementer($className)
	{
		$result = false;
		$declarations = array_merge(class_implements($className), class_parents($className));
		
		foreach ($declarations as $declaration)
		{
			if (isset($this->map[$declaration]))
			{
				$this->map[$declaration][] = $className;
				$result = true;
			}
		}
		
		return $result;
	}
	
	private function findInvalidSkeletons()
	{
		foreach ($this->map as $skeleton => $implementers)
		{
			if (count($implementers) == 1)
			{
				$this->map[$skeleton] = $implementers[0];
				continue;
			}
			
			if (count($implementers) == 0)
			{
				$this->invalidBones[] = [$skeleton => 
					[
						'error'	=> 'Not bones are defined for this skeleton',
						'bones'	=> []
					]
				];
			}
			else
			{
				$this->invalidBones[] = [$skeleton =>
					 [
						 'error'	=> 'More than one bone is defined for this skeleton',
						 'bones'	=> $implementers
					 ]
				];
			}
			
			unset($this->map[$skeleton]);
		}
	}
	
	
	/**
	 * @param array $skeletons
	 * @param array $bones
	 */
	public function __construct(array $skeletons, array $bones) 
	{
		$this->map = array_combine($skeletons, array_fill(0, count($skeletons), []));
		$this->bones = $bones;
	}
	
	
	public function map()
	{
		foreach ($this->bones as $bone)
		{
			if (!$this->mapSingleImplementer($bone))
			{
				$this->invalidBones[] = [$bone => 'Bone does not have a skeleton instance'];
				continue;
			}
			
			$this->validateBone($bone);
		}
		
		$this->findInvalidSkeletons();
	}
	
	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->map;
	}
	
	/**
	 * @return array
	 */
	public function getInvalidSkeletons()
	{
		return $this->invalidSkeletons; 
	}
	
	/**
	 * @return array
	 */
	public function getInvalidBones()
	{
		return $this->invalidBones;
	}
}