<?php
require_once __DIR__ . '/../vendor/autoload.php';


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


// Add auto loader for the test files
spl_autoload_register(
	function ($className)
	{
		if (substr($className, 0, strlen('Tests')) != 'Tests') 
			return;
		
		$classPath = __DIR__ . '/../' . str_replace('\\', '/', $className) . '.php';
		
		require_once $classPath;
	}, 
	true, 
	false	// Append to end
);



class SkeletonTestCase extends TestCase
{
	/**
	 * @param string $target
	 * @return MockObject
	 */
	public function getMock(string $target)
	{
		return $this->getMockBuilder($target)->getMock();
	}
}