<?php
namespace Skeleton\Maps;


use Skeleton\Type;
use Skeleton\Base\IMap;

use Skeleton\Exceptions;


class TestMap extends BaseMap implements IMap
{
	/** @var IMap */
	private $originalMap;
	
	/** @var array */
	private $overrideMap;
	
	
	/**
	 * @param IMap $main
	 */
	public function __construct(IMap $main)
	{
		parent::__construct($main->loader());
		$this->originalMap = $main;
	}
	
	
	/**
	 * @return IMap
	 */
	public function getOriginal() 
	{
		return $this->originalMap;
	}
	
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		$this->originalMap->set($key, $value, $flags);
	}
	
	/**
	 * @param string $key
	 * @return string|object
	 */
	public function get($key) 
	{
		if (isset($this->overrideMap[$key]))
		{
			$value = $this->overrideMap[$key];
			
			if (is_string($value) && class_exists($value))
			{
				return new $value;
			}
			else
			{
				return $value;
			}
		}
		
		return $this->originalMap->get($key);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		return (isset($this->overrideMap[$key]) || $this->originalMap->has($key));
	}
	
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function forceSet($key, $value, $flags = Type::Instance)
	{
		$this->originalMap->forceSet($key, $value, $flags);
	}
	
	
	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function override($key, $value) 
	{
		$this->overrideMap[$key] = $value;
	}
	
	public function clear()
	{
		$this->overrideMap = [];
	}
}