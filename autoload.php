<?php

// Always include this file.
require_once realpath(__DIR__ . '/Skeleton/Base/IConfigLoader.php');
require_once realpath(__DIR__ . '/Skeleton/Base/IMap.php');
require_once realpath(__DIR__ . '/Skeleton/Skeleton.php');
require_once realpath(__DIR__ . '/Skeleton/Type.php');

// Update include path.
set_include_path(
	implode(
		PATH_SEPARATOR, 
		array(
			get_include_path(),
    		realpath(__DIR__)
		)
	)
);

// Add auto loader.
spl_autoload_register(
	function ($className)
	{
		if (substr($className, 0, strlen('Skeleton')) != 'Skeleton') 
			return;
		
		$classPath = str_replace('\\', '/', $className) . '.php';
		
		require_once $classPath;
	}, 
	true, 
	false);