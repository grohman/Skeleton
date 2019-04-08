<?php
namespace Skeleton;


class Type
{
	/** @var int Create a new instance for each request */
	const Instance	= 0;
	
	/** @var int Always return same instance */
	const Singleton	= 1;
	
	/** @var int Always return the value even if it's a class name */
	const ByValue	= 2;
}