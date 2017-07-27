<?php
namespace Skeleton\Tools;


use Skeleton\Context;
use Skeleton\ContextReference;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IContextReference;

use Objection\TStaticClass;
use Skeleton\Exceptions\MissingContextException;


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
		if (!isset($instance->{self::CONTEXT_PROPERTY_NAME}))
			throw new MissingContextException('There is not context configured for class ' . get_class($instance));
		
		return $instance->{self::CONTEXT_PROPERTY_NAME};
	}
	
	public static function create($instance, ISkeletonSource $skeleton, string $name): Context
	{
		if (!isset($instance->{self::CONTEXT_PROPERTY_NAME}))
		{
			$context = new Context($name);
			$ref = new ContextReference($context, $skeleton);
			$instance->{self::CONTEXT_PROPERTY_NAME} = $ref;
			
			return $context;
		}
		
		return $instance->{self::CONTEXT_PROPERTY_NAME}->context();
	}
}