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


	/**
	 * @expectedException \Skeleton\Exceptions\MissingContextException
	 */
	public function test_get_ContextReferenceNotSet_ExceptionThrown()
	{
		$inst = new class {};
		ContextManager::get($inst);
	}
	
	public function test_get_ContextReferenceSet_ContextReferenceReturned()
	{
		$ref = new ContextReference(new Context('a'), new Skeleton());
		$inst = new class {};
		
		ContextManager::set($inst, $ref);
		
		self::assertEquals($ref, ContextManager::get($inst));
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
}