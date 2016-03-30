<?php
namespace Skeleton\Exceptions;


class InvalidImplementerException extends SkeletonException
{
	public function __construct($implementer)
	{
		$type = gettype($implementer);
		parent::__construct("Invalid value. Value must be of type string or object, got $type instead", 4);
	}
}