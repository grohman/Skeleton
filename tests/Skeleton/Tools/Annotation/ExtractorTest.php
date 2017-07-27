<?php
namespace Skeleton\Tools\Annotation;


class ExtractorTest extends \SkeletonTestCase
{
	public function test_has_MatchClassName()
	{
		$this->assertTrue(Extractor::has(test_ExtractorTest_HelperClassA::class, 'has'));
		$this->assertFalse(Extractor::has(test_ExtractorTest_HelperClassB::class, 'has'));
	}
	
	public function test_has_MatchObjectInstance()
	{
		$this->assertTrue(Extractor::has(new test_ExtractorTest_HelperClassA(), 'has'));
		$this->assertFalse(Extractor::has(new test_ExtractorTest_HelperClassB(), 'has'));
	}
	
	public function test_has_MatchReflection()
	{
		$this->assertTrue(Extractor::has(new \ReflectionClass(test_ExtractorTest_HelperClassA::class), 'has'));
		$this->assertFalse(Extractor::has(new \ReflectionClass(test_ExtractorTest_HelperClassB::class), 'has'));
	}
	
	public function test_has_MatchReflectionElement()
	{
		$this->assertTrue(Extractor::has(new \ReflectionProperty(test_ExtractorTest_HelperClassA::class, 'a'), 'has'));
		$this->assertFalse(Extractor::has(new \ReflectionProperty(test_ExtractorTest_HelperClassB::class, 'a'), 'has'));
	}
	
	public function test_has_MatchArray()
	{
		$this->assertTrue(Extractor::has(new \ReflectionProperty(test_ExtractorTest_HasArrayOfValues::class, 'a'), ['arrayAnnotation', 'testArray']));
		$this->assertTrue(Extractor::has(new \ReflectionProperty(test_ExtractorTest_HasArrayOfValues::class, 'a'), ['arrayAnnotation']));
		$this->assertFalse(Extractor::has(new \ReflectionProperty(test_ExtractorTest_HasArrayOfValues::class, 'a'), ['has', 'no']));
	}
	
	
	public function test_has_ClassWithNoAnnotations_ReturnFalse()
	{
		$this->assertFalse(Extractor::has(test_ExtractorTest_HelperClassB::class, 'ann'));
	}
	
	public function test_has_ClassWithAnnotations_ReturnFalse()
	{
		$this->assertFalse(Extractor::has(test_ExtractorTest_HelperClassA::class, 'ann'));
	}
	
	public function test_has_ClassWithAskedAnnotations_ReturnTrue()
	{
		$this->assertTrue(Extractor::has(test_ExtractorTest_HelperClassA::class, 'has'));
	}
	
	public function test_has_CommentIsAllowedAndPresent_ReturnTrue()
	{
		$this->assertTrue(Extractor::has(test_ExtractorTest_HelperClass_WithComment::class, 'has', true));
	}
	
	public function test_has_CommentIsAllowedAndNoComment_ReturnTrue()
	{
		$this->assertTrue(Extractor::has(test_ExtractorTest_HelperClassA::class, 'has'));
	}
	
	public function test_has_CommentIsNotAllowedButPresent_ReturnFalse()
	{
		$this->assertFalse(Extractor::has(test_ExtractorTest_HelperClass_WithComment::class, 'has', false));
	}
	
	public function test_has_CommentIsNotAllowedAndNotPresent_ReturnTrue()
	{
		$this->assertTrue(Extractor::has(test_ExtractorTest_HelperClassA::class, 'has'));
	}
	
	
	public function test_get_ClassWithNoAnnotations_ReturnFalse()
	{
		$this->assertFalse(Extractor::get(test_ExtractorTest_HelperClassB::class, 'get'));
	}
	
	public function test_get_ClassWithAnnotations_ValueNotFound_ReturnFalse()
	{
		$this->assertFalse(Extractor::get(test_ExtractorTest_HelperClass_WithComment::class, 'get'));
	}
	
	public function test_get_ClassWithAnnotations_ValueFound_ReturnValue()
	{
		$this->assertEquals('ABS', Extractor::get(test_ExtractorTest_HelperClassA::class, 'get'));
	}
	
	public function test_get_AnnotationHasComment_ReturnValueOnly()
	{
		$this->assertEquals('AB\S', Extractor::get(test_ExtractorTest_HelperClassA::class, 'get-with-comment'));
	}
	
	
	public function test_getParameterType_ClassWithNoAnnotations_ReturnFalse()
	{
		$this->assertFalse(Extractor::getParameterType(test_ExtractorTest_HelperClassB::class, 'withDollar'));
	}
	
	public function test_getParameterType_ClassWithAnnotations_ValueNotFound_ReturnFalse()
	{
		$this->assertFalse(Extractor::get(test_ExtractorTest_HelperClass_WithComment::class, 'NotFound'));
	}
	
	public function test_getParameterType_TypeFound_ReturnType()
	{
		$this->assertEquals('some\type', Extractor::getParameterType(test_ExtractorTest_HelperClassA::class, 'withDollar'));
	}
	
	public function test_getParameterType_VariableNameMissingDollar_ReturnType()
	{
		$this->assertEquals('some\type', Extractor::getParameterType(test_ExtractorTest_HelperClassA::class, 'noDollar'));
	}
	
	public function test_getParameterType_HasComment_ReturnTypeOnly()
	{
		$this->assertEquals('some\type', Extractor::getParameterType(test_ExtractorTest_HelperClassA::class, 'withComment'));
	}
	
	public function test_getParameterType_InvalidType_ReturnFalse()
	{
		$this->assertFalse(Extractor::getParameterType(test_ExtractorTest_HelperClassA::class, 'type'));
	}
	
	public function test_getParameterType_MissingType_ReturnFalse()
	{
		$this->assertFalse(Extractor::getParameterType(test_ExtractorTest_HelperClassA::class, 'noType'));
	}
}


/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedNamespaceInspection */
/**
 * @has
 * @get ABS
 * @get-with-slash AB\S
 * @get-with-comment AB\S asd params
 * @var some\type $withDollar
 * @var some\type noDollar
 * @var some\type $withComment Comment abc
 * @var some\type invalid $type
 * @var $noType
 */
class test_ExtractorTest_HelperClassA
{
	/**
	 * @has
	 */
	public $a;
}


class test_ExtractorTest_HasArrayOfValues
{
	/**
	 * @arrayAnnotation
	 * @testArray
	 */
	public $a;
}

class test_ExtractorTest_HelperClassB
{
	public $a;
}


/** @noinspection PhpUndefinedClassInspection */
/**
 * @has with comment
 * @var not $Found
 */
class test_ExtractorTest_HelperClass_WithComment
{
	/**
	 * @has
	 */
	public $a;
}
