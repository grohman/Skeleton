<?php
namespace Skeleton\ImplementersMap;


use Skeleton\Type;
use Skeleton\Base\IMap;

use Skeleton\Exceptions;


class SimpleMap implements IMap 
{
	/** @var array */
	private $m_aValues = [];
	
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		$this->m_aValues[$key] = $value;
	}
	
	/**
	 * @param string $key
	 * @return string|object
	 */
	public function get($key) 
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		if (isset($this->m_aValues[$key]))
			return $this->m_aValues[$key];
		
		throw new Exceptions\ImplementerNotDefinedException($key);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		return isset($this->m_aValues[$key]);
	}
	
	public function reset()
	{
		$this->m_aValues = [];
	}
}