<?php
namespace Skeleton\ImplementersMap;


use Skeleton\Type;
use Skeleton\Base\IMap;

use Skeleton\Exceptions;


class TestMap extends SimpleMap
{
	/** @var IMap */
	private $m_mapMain;
	
	
	/**
	 * @param IMap $main
	 */
	public function __construct(IMap $main)
	{
		$this->m_mapMain = $main;
	}
	
	
	/**
	 * @return IMap
	 */
	public function getMainMap() 
	{
		return $this->m_mapMain;
	}
	
	
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		$this->m_mapMain->set($key, $value, $flags);
	}
	
	/**
	 * @param string $key
	 * @return string|object
	 */
	public function get($key) 
	{
		if (parent::has($key))
		{
			return parent::get($key);
		}
		
		return $this->m_mapMain->get($key);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		return (parent::has($key) || $this->m_mapMain->has($key));
	}
	
	
	/**
	 * @param string $key
	 * @param string|object $implementer
	 * @param int $flags
	 */
	public function override($key, $implementer, $flags = Type::Instance) 
	{
		parent::set($key, $implementer, $flags);
	}
}