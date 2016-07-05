<?php
namespace Skeleton\Tools\Knot;


class KnotConsts
{
	use \Objection\TConstsClass;
	
	
	const AUTOLOAD_ANNOTATION				= 'autoload';
	const VARIABLE_DECLARATION_ANNOTATION	= 'var';
	
	const AUTOLOAD_METHOD_PREFIX			= 'set';
}