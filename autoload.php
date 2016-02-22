<?php
require_once __DIR__ . '/Skeleton/inc.php';


// Add auto loader.
spl_autoload_register(
	function ($className)
	{
		if (substr($className, 0, strlen('Skeleton')) != 'Skeleton') 
			return;
		
		$classPath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', '/', $className) . '.php';
		
		require_once $classPath;
	}, 
	true, 
	false	// Append to end
);