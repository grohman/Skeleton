<?php
namespace Skeleton\Base;


interface IContextSource
{
	public function has(string $key): bool;

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key);
}