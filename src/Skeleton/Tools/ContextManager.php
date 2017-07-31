<?php
namespace Skeleton\Tools;


use Skeleton\Context;
use Skeleton\ContextReference;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Base\IContextReference;
use Skeleton\Exceptions\SkeletonException;
use Skeleton\Exceptions\MissingContextException;

use Objection\TStaticClass;


class ContextManager
{
	use TStaticClass;
	
	
	public const CONTEXT_PROPERTY_NAME	= '__SKELETON_CONTEXT__';


	/**
	 * @param ISkeletonSource $source
	 * @param array|Context $data
	 * @param string|null $name
	 * @return ContextReference
	 */
	public static function create(ISkeletonSource $source, $data, ?string $name = null)
	{
		if (is_array($data))
		{
			$context = new Context($name, $data);
			return new ContextReference($context, $source);
		}	
		else if ($data instanceof Context)
		{
			return new ContextReference($data, $source);
		}
		
		throw new SkeletonException('Context must be Context instance, IContextReference instance or array');
	}
		
	public static function set($instance, IContextReference $context)
	{
		$instance->{self::CONTEXT_PROPERTY_NAME} = $context;
	}
	
	public static function get($instance, ISkeletonSource $source): IContextReference
	{
		if (is_array($instance))
		{
			$context = new Context(null, $instance);
			return new ContextReference($context, $source);
		}
		
		if (!isset($instance->{self::CONTEXT_PROPERTY_NAME}))
			throw new MissingContextException('There is not context configured for class ' . get_class($instance));
		
		return $instance->{self::CONTEXT_PROPERTY_NAME};
	}
	
	public static function init($instance, ISkeletonSource $skeleton, ?string $name = null): Context
	{
		if (is_array($instance))
		{
			return new Context($name, $instance);
		}
		else if (!isset($instance->{self::CONTEXT_PROPERTY_NAME}))
		{
			$context = new Context($name);
			$ref = new ContextReference($context, $skeleton);
			$instance->{self::CONTEXT_PROPERTY_NAME} = $ref;
			
			return $context;
		}
		
		return $instance->{self::CONTEXT_PROPERTY_NAME}->context();
	}
}