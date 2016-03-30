<?php
namespace Skeleton\Exceptions;


class SkeletonException extends \Exception
{
	public function __construct($message, $code = -1, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}