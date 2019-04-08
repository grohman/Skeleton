<?php
namespace Skeleton\Exceptions;


class InvalidKeyException extends SkeletonException
{
	public function __construct($key)
	{
		$type = gettype($key);
		parent::__construct("Invalid key. Expecting string, got $type instead", 3);
	}
}