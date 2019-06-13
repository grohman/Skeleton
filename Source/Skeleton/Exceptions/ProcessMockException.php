<?php
namespace Skeleton\Exceptions;


class ProcessMockException extends SkeletonFatalException
{
	public function __construct(string $message, ?string $mockFile = null)
	{
		if ($mockFile)
			$message = "Exception with mock file '$mockFile': $message";
		
		parent::__construct($message);
	}
}