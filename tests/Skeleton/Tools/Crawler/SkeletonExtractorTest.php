<?php
namespace Skeleton\Tools\Crawler;


class SkeletonExtractorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param SkeletonExtractor $extractor
	 */
	private function assertNothingExtracted(SkeletonExtractor $extractor)
	{
		$this->assertEmpty($extractor->getImplementers());
		$this->assertEmpty($extractor->getInterfaces());	
	}
	
	/**
	 * @param SkeletonExtractor $extractor
	 * @param string $declarationName
	 */
	private function assertHasSkeletonDeclaration(SkeletonExtractor $extractor, $declarationName)
	{
		$this->assertEmpty($extractor->getImplementers());
		$this->assertEquals([$declarationName], $extractor->getInterfaces());
	}
	
	/**
	 * @param SkeletonExtractor $extractor
	 * @param string $declarationName
	 */
	private function assertHasBoneDeclaration(SkeletonExtractor $extractor, $declarationName)
	{
		$this->assertEquals([$declarationName], $extractor->getImplementers());
		$this->assertEmpty($extractor->getInterfaces());
	}
	
	/**
	 * @param string $className
	 */
	private function assertClassNotExtracted($className)
	{
		$extractor = new SkeletonExtractor();
		$extractor->extract([$className]);
		$this->assertNothingExtracted($extractor);
	}
	
	/**
	 * @param string $className
	 */
	private function assertSkeletonClassExtracted($className)
	{
		$extractor = new SkeletonExtractor();
		$extractor->extract([$className]);
		$this->assertHasSkeletonDeclaration($extractor, $className);
	}
	
	/**
	 * @param string $className
	 */
	private function assertBoneClassExtracted($className)
	{
		$extractor = new SkeletonExtractor();
		$extractor->extract([$className]);
		$this->assertHasBoneDeclaration($extractor, $className);
	}
	
	
	public function test_extract_EmptyDeclarations_ReturnEmptyArray()
	{
		$extractor = new SkeletonExtractor();
		$extractor->extract([]);
		$this->assertNothingExtracted($extractor);
	}
	
	public function test_extract_ClassWithoutAnnotations_Ignored()
	{
		$this->assertClassNotExtracted(SkeletonExtractorTest_EmptyClass::class);
	}
	
	public function test_extract_ClassWithRandomComment_Ignored()
	{
		$this->assertClassNotExtracted(SkeletonExtractorTest_RandomComment::class);
	}
	
	public function test_extract_ClassWithSkeletonButInMiddleOfComment_Ignored()
	{
		$this->assertClassNotExtracted(SkeletonExtractorTest_ClassWithIncorrectComment::class);
	}
	
	public function test_extract_MarkedAsSkeleton_ClassReturnedAsSkeleton()
	{
		$this->assertSkeletonClassExtracted(SkeletonExtractorTest_ValidSkeletonClass::class);
	}
	
	public function test_extract_MarkedAsBone_ClassReturnedAsSkeleton()
	{
		$this->assertBoneClassExtracted(SkeletonExtractorTest_ValidBoneClass::class);
	}
	
	public function test_extract_NumberOfClasses()
	{
		$extractor = new SkeletonExtractor();
		$extractor->extract([
			SkeletonExtractorTest_EmptyClass::class, 
			SkeletonExtractorTest_ValidSkeletonClass::class,
			SkeletonExtractorTest_ClassWithIncorrectComment::class,
			SkeletonExtractorTest_ValidBoneClass::class,
			SkeletonExtractorTest_ValidBoneClass_B::class
		]);
		
		$this->assertEquals([SkeletonExtractorTest_ValidSkeletonClass::class], $extractor->getInterfaces());
		$this->assertEquals(
			[
				SkeletonExtractorTest_ValidBoneClass::class,
				SkeletonExtractorTest_ValidBoneClass_B::class,
			], 
			$extractor->getImplementers());
	}
}


class SkeletonExtractorTest_EmptyClass {}

/**
 * @not-a-skeleton
 * and some comment
 */
class SkeletonExtractorTest_RandomComment {}

/**
 * ab @skeleton
 */
class SkeletonExtractorTest_ClassWithIncorrectComment {}

/**
 * @skeleton
 */
class SkeletonExtractorTest_ValidSkeletonClass {}

/**
 * @bone
 */
class SkeletonExtractorTest_ValidBoneClass {}

/**
 * @bone
 */
class SkeletonExtractorTest_ValidBoneClass_B {}