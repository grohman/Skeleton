<?php
namespace Skeleton\Maps;


use Skeleton\Type;
use Skeleton\ISingleton;
use Skeleton\Base\IMap;
use Skeleton\Base\IContextReference;
use Skeleton\Tools\Annotation\Extractor;

use Skeleton\Exceptions;


class SimpleMap extends BaseMap implements IMap 
{
	/** @var array */
	private $config = [];
	
	/** @var array */
	private $resolvedValues = [];
	
	
	/**
	 * @param string $key
	 * @return object|mixed
	 */
	private function getObject($key)
	{
		$value = $this->config[$key][0];
		$type = $this->config[$key][1];
		
		if ((is_string($value) && class_exists($value)) || is_object($value)) 
		{
			$type = $this->getFlagFromClass($type, $value);
			
			if ($type == Type::ByValue)
			{
				$this->resolvedValues[$key] = $value;
				return $value;
			}
		}
		
		$instance = $this->loader()->get($value);
		
		if ($instance instanceof ISingleton || $type == Type::Singleton)
		{
			$this->resolvedValues[$key] = $instance;
			unset($this->config[$key]);
		}
		
		return $instance;
	}
	
	/**
	 * @param int $originalFlag
	 * @param string $className
	 * @return int
	 */
	private function getFlagFromClass($originalFlag, $className)
	{
		if (Extractor::has($className, 'static'))
			return Type::ByValue;
		
		if (Extractor::has($className, 'unique'))
			return Type::Singleton;
		
		return $originalFlag;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		if ($this->has($key))
			throw new Exceptions\ImplementerAlreadyDefinedException($key);
		
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
	 * @param string|object $value
	 * @param int $flags
	 */
	public function forceSet($key, $value, $flags = Type::Instance)
	{
		if ((is_string($value) || is_callable($value)) && $flags != Type::ByValue)
		{
			$this->config[$key] = [$value, $flags];
			unset($this->resolvedValues[$key]);
		}
		else
		{
			$this->resolvedValues[$key] = $value;
			unset($this->config[$key]);
		}
	}
	
	/**
	 * @param string $key
	 * @param IContextReference|null $context
	 * @return string|object
	 */
	public function get(string $key, ?IContextReference $context = null)
	{
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