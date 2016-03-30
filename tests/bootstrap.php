<?php
require_once __DIR__ . '/../vendor/autoload.php';


// Add auto loader for the test files
spl_autoload_register(
	function ($className)
	{
		if (substr($className, 0, strlen('test')) != 'test') 
			return;
		
		$classPath = __DIR__ . '/../' . str_replace('\\', '/', $className) . '.php';
		
		require_once $classPath;
	}, 
	true, 
	false	// Append to end
);