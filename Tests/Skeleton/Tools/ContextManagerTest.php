<?php
namespace Skeleton\Tools;


use Skeleton\Context;
use Skeleton\Skeleton;
use Skeleton\ContextReference;

use PHPUnit\Framework\TestCase;


class ContextManagerTest extends TestCase
{
	public function test_set_ContextReferenceSet()
	{
		$ref = new ContextReference(new Context('a'), new Skeleton());
		$inst = new class {};
		
		ContextManager::set($inst, $ref);
		
		self::assertEquals($ref, $inst->{ContextManager::CONTEXT_PROPERTY_NAME});
	}
	
	public function test_get_ContextReferenceNotSet_ExceptionThrown()
	{
		$this->expectException(\Skeleton\Exceptions\MissingContextException::class);
		
		$inst = new class {};
		ContextManager::get($inst, new Skeleton());
	}
	
	public function test_get_ContextReferenceSet_ContextReferenceReturned()
	{
		$ref = new ContextReference(new Context('a'), new Skeleton());
		$inst = new class {};
		
		ContextManager::set($inst, $ref);
		
		self::assertEquals($ref, ContextManager::get($inst, new Skeleton()));
	}
	
	public function test_get_ArrayPassed_NewContextReferenceCreated()
	{
		$result = ContextManager::get(['a' => 'b'], new Skeleton());
		self::assertEquals('b', $result->context()->get('a'));
	}
	
	public function test_get_ArrayPassed_ContextHaveDefaultName()
	{
		$result = ContextManager::get(['a' => 'b'], new Skeleton());
		
		/** @noinspection PhpUndefinedMethodInspection */
		self::assertEquals('context', $result->context()->name());
	}
	
	
	public function test_init_ContextObjectReturned()
	{
		$inst = new class {};
		
		$res = ContextManager::init($inst, new Skeleton(), 'a');
		
		self::assertInstanceOf(Context::class, $res);
	}
	
	public function test_init_ContextAlreadyExists_SameObjectReturned()
	{
		$inst = new class {};
		
		$res1 = ContextManager::init($inst, new Skeleton(), 'a');
		$res2 = ContextManager::init($inst, new Skeleton(), 'a');
		
		self::assertEquals($res1, $res2);
	}
	
	public function test_init_ContextNameIsSetCorrectly()
	{
		$inst = new class {};
		
		$res = ContextManager::init($inst, new Skeleton(), 'a');
		
		self::assertEquals('a', $res->name());
	}
	
	public function test_init_ArrayPassed_ContextCreatedFromArray()
	{
		$res = ContextManager::init(['a' => 'b'], new Skeleton(), 'a');
		
		self::assertEquals('b', $res->get('a'));
	}
	
	public function test_init_ArrayPassed_NewContextHavePassedName()
	{
		$res = ContextManager::init([], new Skeleton(), 'a');
		
		self::assertEquals('a', $res->name());
	}
	
	
	public function test_create_PassArray_ContextCreated()
	{
		$res = ContextManager::create(new Skeleton(), ['a' => 'b']);
		self::assertEquals('b', $res->context()->get('a'));
	}
	
	public function test_create_PassArray_ContextNameIsCorrect()
	{
		$res = ContextManager::create(new Skeleton(), [], 'name');
		
		/** @noinspection PhpUndefinedMethodInspection */
		self::assertEquals('name', $res->context()->name());
	}
	
	public function test_create_ContextInstancePassed_InstanceUsed()
	{
		$context = new Context(['a' => 'b']);
		$res = ContextManager::create(new Skeleton(), $context);
		self::assertEquals($context, $res->context());
	}
	
	public function test_create_InvalidTypePassed_ExceptionThrown()
	{
		$this->expectException(\Skeleton\Exceptions\SkeletonException::class);
		
		/** @noinspection PhpParamsInspection */
		ContextManager::create(new Skeleton(), new \stdClass());
	}
}