<?php
namespace Skeleton\Maps;


use Skeleton\Type;
use Skeleton\ISingleton;
use Skeleton\Base\IMap;
use Skeleton\Base\ILoader;

use Skeleton\Exceptions;


class SimpleMap implements IMap 
{
	/** @var array */
	private $config = [];
	
	/** @var array */
	private $resolvedValues = [];
	
	/** @var ILoader */
	private $loader = null;
	
	
	/**
	 * @param string $key
	 * @return object
	 */
	private function getObject($key)
	{
		$value = $this->config[$key][0];
		$type = $this->config[$key][1];
		
		$instance = $this->loader->get($value);
		
		if ($instance instanceof ISingleton || $type == Type::Singleton)
		{
			$this->resolvedValues[$key] = $instance;
			unset($this->config[$key]);
		}
		
		return $instance;
	}
	
	
	/**
	 * @param ILoader $loader
	 */
	public function setLoader(ILoader $loader)
	{
		$this->loader = $loader;
	}
	
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		if ($this->has($key))
			throw new Exceptions\ImplementerAlreadyDefinedException($key);
		
		// Save only the values that should be resolved in the config array.
		if ((is_string($value) || is_callable($value)) && $flags != Type::ByValue) 
		{
			$this->config[$key] = [$value, $flags];
		} 
		else 
		{
			$this->resolvedValues[$key] = $value;
		}
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) 
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		if (key_exists($key, $this->resolvedValues))
		{
			return $this->resolvedValues[$key];
		}
		else if (!isset($this->config[$key]))
		{
			throw new Exceptions\ImplementerNotDefinedException($key);
		}
		
		return $this->getObject($key);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		return key_exists($key, $this->resolvedValues) || isset($this->config[$key]);
	}
}