<?php
namespace Skeleton\ImplementersMap;


use \Skeleton\Type;
use \Skeleton\Base\IMap;

use \Skeleton\Exceptions;


class SimpleMap implements IMap 
{
	private $m_arrMap = [];
	
	
	/**
	 * @param string $key
	 * @param string $value
	 * @param string $type
	 * @return object
	 */
	private function getObject($key, $value, $type)
	{
		switch ($type)
		{
			case Type::Instance:
				return new $value;
				
			case Type::Singleton:
				$instance = new $value;
				$this->m_arrMap[$key] = $instance;
				return $instance;
				
			default:
				throw new Exceptions\SkeletonException("Type $type is not expected at this point!");
		}
	}
	
	
	/**
	 * @param string $key
	 * @param string|object $implementer
	 * @param int $flags
	 */
	public function set($key, $implementer, $flags = Type::Instance)
	{
		if (isset($this->m_arrMap[$key]))
			throw new Exceptions\ImplementerAlreadyDefinedException($key);
		
		if (is_string($implementer) && $flags != Type::StaticClass) 
		{
			$this->m_arrMap[$key] = [$implementer, $flags];
		} 
		else 
		{
			$this->m_arrMap[$key] = $implementer;
		}
	}
	
	/**
	 * @param string $key
	 * @return string|object
	 */
	public function get($key) 
	{
		if (!isset($this->m_arrMap[$key]))
			throw new Exceptions\ImplementerNotDefinedException($key);
		
		$data = $this->m_arrMap[$key];
		
		if (!is_array($data)) 
			return $data;
		
		return $this->getObject($key, $data[0], $data[1]);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		return isset($this->m_arrMap[$key]);
	}
}