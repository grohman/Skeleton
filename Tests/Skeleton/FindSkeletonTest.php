<?php
namespace Skeleton;


use PHPUnit\Framework\TestCase;


class FindSkeletonTest extends TestCase
{
	public function test_get_PassNonExistingName_NullReturned()
	{
		self::assertNull(FindSkeleton::getSkeleton('Hello\\World\\Wha\\What'));
	}
	
	public function test_get_PassExistingClass_ObjectReturned()
	{
		require_once __DIR__ . '/FindSkeleton/ShortNamespaceChild.php';
		
		self::assertSame(
			\FindSkeletonTestA\SkeletonInit::$skeleton, 
			FindSkeleton::getSkeleton('\\FindSkeletonTestA\\Hello\\World')
		);
	}
	
	public function test_get_PassExistingClassInASubDirectory_ObjectReturned()
	{
		require_once __DIR__ . '/FindSkeleton/LongNamespaceChild.php';
		
		self::assertSame(
			\FindSkeletonTestB\Hello\World\SkeletonInit::$skeleton, 
			FindSkeleton::getSkeleton('\\FindSkeletonTestB\\Hello\\World\\AB\\CD')
		);
	}
}