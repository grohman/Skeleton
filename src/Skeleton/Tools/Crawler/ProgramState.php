<?php
namespace Skeleton\Tools\Crawler;


class ProgramState
{
	private $declarations;
	private $globalNames;
	
	/**
	 * @return array
	 */
	private function getDeclarations()
	{
		return array_merge(get_declared_classes(), get_declared_interfaces());
	}
	
	
	public function saveState()
	{
		$this->declarations = $this->getDeclarations();
		$this->globalNames = array_keys($GLOBALS);
	}
	
	/**
	 * @return array
	 */
	public function getNewDeclarations()
	{
		return array_values(array_diff($this->getDeclarations(), $this->declarations));
	}
}