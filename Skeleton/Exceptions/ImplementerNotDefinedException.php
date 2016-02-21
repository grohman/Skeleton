<?php
namespace Skeleton\Exceptions;


class ImplementerNotDefinedException extends SkeletonException
{
	public function __construct($key)
	{
		parent::__construct("Implementer for the key '$key' is not defined", 2);
	}
}