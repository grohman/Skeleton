<?php
namespace Skeleton\Maps;


use Skeleton\Type;
use Skeleton\Base\IMap;
use Skeleton\Base\IContextReference;


/**
 * @deprecated 
 */
class TestMap extends BaseMap implements IMap
{
	/** @var IMap */
	private $originalMap;
	
	/** @var array */
	private $overrideMap = [];
	
	
	public function __construct(IMap $main)
	{
		parent::__construct($main->loader());
		$this->originalMap = $main;
	}
	
	
	public function getOriginal(): IMap
	{
		return $this->originalMap;
	}
	
	public function set(string $key, $value, int $flags = Type::Instance): void
	{
		$this->originalMap->set($key, $value, $flags);
	}
	
	public function forceSet(string $key, $value, int $flags = Type::Instance): void
	{
		$this->originalMap->forceSet($key, $value, $flags);
	}
	
	/**
	 * @param string $key
	 * @param IContextReference|null $context
	 * @return string|object
	 */
	public function get(string $key, ?IContextReference $context = null)
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
		
		return $this->originalMap->get($key, $context);
	}
	
	public function has(string $key): bool 
	{
		return (isset($this->overrideMap[$key]) || $this->originalMap->has($key));
	}
	
	
	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function override(string $key, $value): void 
	{
		$this->overrideMap[$key] = $value;
	}
	
	public function clear(): void
	{
		$this->overrideMap = [];
	}
	
	public function asArray(): array 
	{
		return $this->overrideMap;
	}
}