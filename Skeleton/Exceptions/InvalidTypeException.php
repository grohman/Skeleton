<?php
namespace Skeleton\Exceptions;


class InvalidTypeException extends SkeletonException
{
	public function __construct($type)
	{
		parent::__construct("Invalid type '$type'", 5);
	}
}