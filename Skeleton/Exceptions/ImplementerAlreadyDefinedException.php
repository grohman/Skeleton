<?php
namespace Skeleton\Exceptions;


class ImplementerAlreadyDefinedException extends SkeletonException
{
	public function __construct($key)
	{
		parent::__construct("Implementer for the key '$key' is already defined", 1);
	}
}