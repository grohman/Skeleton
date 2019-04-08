<?php
namespace Skeleton\Base;


interface IContextReference
{
	public function context(): IContextSource;

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key);

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function load(string $key);
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function value(string $key);
}