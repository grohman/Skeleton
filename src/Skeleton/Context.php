<?php
namespace Skeleton;


use Skeleton\Exceptions\ContextNotDefinedException;


class Context
{
	private $name;
	private $context = [];
	
	
	public function __construct(string $name = 'context')
	{
		$this->name = $name;
	}


	public function name(): string
	{
		return $this->name;
	}
	
	public function has(string $key): bool
	{
		return isset($this->context[$key]);
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
			throw new ContextNotDefinedException($this->name, $key);
		
		return $this->context[$key];
	}
}