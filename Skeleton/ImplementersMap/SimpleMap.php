<?php
namespace Skeleton\ImplementersMap;


use \Skeleton\Type;
use \Skeleton\ISingleton;
use \Skeleton\Base\IMap;

use \Skeleton\Exceptions;


class SimpleMap implements IMap 
{
	/** @var array */
	private $m_aMap = [];
	
	
	/**
	 * @param string $key
	 * @param string $value
	 * @param string $type
	 * @return object
	 */
	private function getObject($key, $value, $type)
	{
		$instance = new $value;
		
		if ($instance instanceof ISingleton || $type == Type::Singleton)
		{
			$this->m_aMap[$key] = $instance;
		}
		
		return $instance;
	}
	
	
	/**
	 * @param string $key
	 * @param string|object $implementer
	 * @param int $flags
	 */
	public function set($key, $implementer, $flags = Type::Instance)
	{
		if (isset($this->m_aMap[$key]))
			throw new Exceptions\ImplementerAlreadyDefinedException($key);
		
		if (is_string($implementer) && $flags != Type::StaticClass) 
		{
			$this->m_aMap[$key] = [$implementer, $flags];
		} 
		else 
		{
			$this->m_aMap[$key] = $implementer;
		}
	}
	
	/**
	 * @param string $key
	 * @return string|object
	 */
	public function get($key) 
	{
		if (!isset($this->m_aMap[$key]))
			throw new Exceptions\ImplementerNotDefinedException($key);
		
		$data = $this->m_aMap[$key];
		
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
		
		return isset($this->m_aMap[$key]);
	}
}