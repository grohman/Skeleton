<?php
namespace Skeleton\Tools\Knot;


use Traitor\TConstsClass;


class KnotConsts
{
	use TConstsClass;
	
	
	public const AUTOLOAD_ANNOTATIONS				= ['autoload', 'magic'];
	public const VARIABLE_DECLARATION_ANNOTATION	= 'var';
	public const AUTOLOAD_METHOD_PREFIX				= 'set';
	public const CONTEXT_ANNOTATION					= 'context';
}