<?php
namespace Skeleton\ProcessMock\Content;


use Skeleton\TestSkeleton;


function mock(string $key, string $value): void
{
	TestSkeleton::override($key, $value);
}

function mock_value(string $key, $value): void
{
	TestSkeleton::overrideValue($key, $value);
}


// GENERATED CONTENT:

