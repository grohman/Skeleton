<?php
namespace Skeleton\Base;


interface IConfigLoader {
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function tryLoad($path);
}