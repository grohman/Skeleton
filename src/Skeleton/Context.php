<?php
namespace Skeleton;


use Skeleton\Base\IContextSource;
use Skeleton\Exceptions\MissingContextValueException;


class Context implements IContextSource
{
	private $name;
	
	/** @var array */
	private $context;
	
	/** @var null|Context */
	private $parentContext = null;
	
	
	public function __construct(string $name = 'context', ?Context $parent = null)
	{
		$this->name = $name;
		$this->parentContext = $parent;
		
		$this->context = ($parent ? $parent->context : []); 
	}
	
	
	public function name(): string
	{
		return $this->name;
	}
	
	public function has(string $key): bool
	{
		return isset($this->context[$key]) || ($this->parentContext && $this->parentContext->has($key));
	}

	/**
	 * @param string|array $key
	 * @param mixed|null $value
	 * @return Context
	 */
	public function set($key, $value = null): Context
	{
		if (is_string($key))
		{
			$this->context[$key] = $value;
		}
		else if (is_array($key))
		{
			if ($value)
			{
				foreach ($key as $k)
				{
					$this->context[$k] = $value;
				}
			}
			else
			{
				foreach ($key as $k => $v)
				{
					$this->context[$k] = $v;
				}
			}
		}
			
		return $this;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key)
	{
		if (!isset($this->context[$key]))
		{
			if (!$this->parentContext)
				throw new MissingContextValueException($this->name, $key);
			
			try
			{
				$value = $this->parentContext->get($key);
			}
			// Rethrow the exception so context name and callstack will match this point. 
			catch (MissingContextValueException $e)
			{
				throw new MissingContextValueException($this->name, $key);
			}
			
			$this->context[$key] = $value;
			
			return $value;
		}
		
		return $this->context[$key];
	}
}