<?php
namespace Skeleton\Tools;


use Objection\TStaticClass;
use Skeleton\Base\IContextReference;


class ContextManager
{
	use TStaticClass;
	
	
	const CONTEXT_PROPERTY_NAME	= '__SKELETON_CONTEXT__';
	

	public static function set($instance, IContextReference $context)
	{
		
		
		$instance->{self::CONTEXT_PROPERTY_NAME} = $context;
	}
	
	public static function get($instance): IContextReference
	{
		return $instance->{self::CONTEXT_PROPERTY_NAME};
	}
}