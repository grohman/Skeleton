<?php
namespace Skeleton\Tools\Annotation;


class ExtractorTest extends \PHPUnit_Framework_TestCase
{
	public function test_has_MatchClassName()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(test_ExtractorTest_HelperClassA::class, 'has'));
		$this->assertFalse($e->has(test_ExtractorTest_HelperClassB::class, 'has'));
	}
	
	public function test_has_MatchObjectInstance()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(new test_ExtractorTest_HelperClassA(), 'has'));
		$this->assertFalse($e->has(new test_ExtractorTest_HelperClassB(), 'has'));
	}
	
	public function test_has_MatchReflection()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(new \ReflectionClass(test_ExtractorTest_HelperClassA::class), 'has'));
		$this->assertFalse($e->has(new \ReflectionClass(test_ExtractorTest_HelperClassB::class), 'has'));
	}
	
	public function test_has_MatchReflectionElement()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(new \ReflectionProperty(test_ExtractorTest_HelperClassA::class, 'a'), 'has'));
		$this->assertFalse($e->has(new \ReflectionProperty(test_ExtractorTest_HelperClassB::class, 'a'), 'has'));
	}
	
	
	public function test_has_ClassWithNoAnnotations_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertFalse($e->has(test_ExtractorTest_HelperClassB::class, 'ann'));
	}
	
	public function test_has_ClassWithAnnotations_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertFalse($e->has(test_ExtractorTest_HelperClassA::class, 'ann'));
	}
	
	public function test_has_ClassWithAskedAnnotations_ReturnTrue()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(test_ExtractorTest_HelperClassA::class, 'has'));
	}
	
	public function test_has_CommentIsAllowedAndPresent_ReturnTrue()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(test_ExtractorTest_HelperClass_WithComment::class, 'has', true));
	}
	
	public function test_has_CommentIsAllowedAndNoComment_ReturnTrue()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(test_ExtractorTest_HelperClassA::class, 'has'));
	}
	
	public function test_has_CommentIsNotAllowedButPresent_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(test_ExtractorTest_HelperClass_WithComment::class, 'has'));
	}
	
	public function test_has_CommentIsNotAllowedAndNotPresent_ReturnTrue()
	{
		$e = new Extractor();
		$this->assertTrue($e->has(test_ExtractorTest_HelperClassA::class, 'has'));
	}
	
	
	public function test_get_ClassWithNoAnnotations_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertFalse($e->get(test_ExtractorTest_HelperClassB::class, 'get'));
	}
	
	public function test_get_ClassWithAnnotations_ValueNotFound_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertFalse($e->get(test_ExtractorTest_HelperClass_WithComment::class, 'get'));
	}
	
	public function test_get_ClassWithAnnotations_ValueFound_ReturnValue()
	{
		$e = new Extractor();
		$this->assertEquals('ABS', $e->get(test_ExtractorTest_HelperClassA::class, 'get'));
	}
	
	public function test_get_AnnotationHasComment_ReturnValueOnly()
	{
		$e = new Extractor();
		$this->assertEquals('AB\S', $e->get(test_ExtractorTest_HelperClassA::class, 'get-with-comment'));
	}
	
	
	public function test_getParameterType_ClassWithNoAnnotations_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertFalse($e->getParameterType(test_ExtractorTest_HelperClassB::class, 'withDollar'));
	}
	
	public function test_getParameterType_ClassWithAnnotations_ValueNotFound_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertFalse($e->get(test_ExtractorTest_HelperClass_WithComment::class, 'NotFound'));
	}
	
	public function test_getParameterType_TypeFound_ReturnType()
	{
		$e = new Extractor();
		$this->assertEquals('some\type', $e->getParameterType(test_ExtractorTest_HelperClassA::class, 'withDollar'));
	}
	
	public function test_getParameterType_VariableNameMissingDollar_ReturnType()
	{
		$e = new Extractor();
		$this->assertEquals('some\type', $e->getParameterType(test_ExtractorTest_HelperClassA::class, 'noDollar'));
	}
	
	public function test_getParameterType_HasComment_ReturnTypeOnly()
	{
		$e = new Extractor();
		$this->assertEquals('some\type', $e->getParameterType(test_ExtractorTest_HelperClassA::class, 'withComment'));
	}
	
	public function test_getParameterType_InvalidType_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertFalse($e->getParameterType(test_ExtractorTest_HelperClassA::class, 'type'));
	}
	
	public function test_getParameterType_MissingType_ReturnFalse()
	{
		$e = new Extractor();
		$this->assertFalse($e->getParameterType(test_ExtractorTest_HelperClassA::class, 'noType'));
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
