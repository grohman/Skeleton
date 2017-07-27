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
}