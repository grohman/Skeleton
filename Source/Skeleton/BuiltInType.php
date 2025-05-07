<?php
namespace Skeleton;


use Traitor\TConstsClass;


class BuiltInType
{
	use TConstsClass;
	
	
	public const INT 		= 'int';
	public const FLOAT 		= 'float';
	public const BOOL 		= 'bool';
	public const STRING 	= 'string';
	public const ARRAY 		= 'array';
	public const OBJECT 	= 'object';
	public const CALLABLE 	= 'callable';
	public const ITERABLE 	= 'iterable';
	public const MIXED 		= 'mixed';
}