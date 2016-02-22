<?php

// Always include this files.
require_once realpath(__DIR__ . '/Skeleton/Base/IConfigLoader.php');
require_once realpath(__DIR__ . '/Skeleton/Base/IMap.php');
require_once realpath(__DIR__ . '/Skeleton/Base/ConfigSearch.php');
require_once realpath(__DIR__ . '/Skeleton/Skeleton.php');
require_once realpath(__DIR__ . '/Skeleton/Type.php');


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