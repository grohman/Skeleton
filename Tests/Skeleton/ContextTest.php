<?php
namespace Skeleton;


use PHPUnit\Framework\TestCase;


class ContextTest extends TestCase
{
	public function test_constructor_Empty_NameSet()
	{
		$subject = new Context();
		self::assertEquals('context', $subject->name());
	}
	
	public function test_constructor_PassArray_NameSetCorrectly()
	{
		$subject = new Context(['a' => 'b']);
		self::assertEquals('context', $subject->name());
	}
	
	public function test_constructor_PassArray_ContextSet()
	{
		$subject = new Context(['a' => 'b']);
		self::assertEquals('b', $subject->get('a'));
	}
	
	public function test_constructor_PassNameAndArray_NameSetCorrectly()
	{
		$subject = new Context('c', ['a' => 'b']);
		self::assertEquals('c', $subject->name());
	}
	
	public function test_constructor_PassNameAndArray_ContextSet()
	{
		$subject = new Context('c', ['a' => 'b']);
		self::assertEquals('b', $subject->get('a'));
	}
	
	
	
	public function test_name()
	{
		$subject = new Context('a');
		self::assertEquals('a', $subject->name());
	}
	
	
	public function test_has_NotFound_ReturnFalse()
	{
		$subject = new Context('a');
		self::assertFalse($subject->has('a'));
	}
	
	public function test_has_Found_ReturnTrue()
	{
		$subject = new Context('a');
		$subject->set('b', 123);
		
		self::assertTrue($subject->has('b'));
	}
	
	public function test_has_ParentPresent_KeyNotFound_ReturnFalse()
	{
		$subject = new Context('a', new Context('b'));
		self::assertFalse($subject->has('a'));
	}
	
	public function test_has_KeyAlreadyExistsInParent_ReturnTrue()
	{
		$parent = new Context('b');
		$parent->set('b', 123);
		
		$subject = new Context('a', $parent);
		
		self::assertTrue($subject->has('b'));
	}
	
	public function test_has_KeyAddToParentAfterConstructor_ReturnTrue()
	{
		$parent = new Context('b');
		
		$subject = new Context('a', $parent);
		$parent->set('b', 123);
		
		self::assertTrue($subject->has('b'));
	}
	
	
	public function test_set_SimpleParameters()
	{
		$subject = new Context('a');
		
		$subject->set('a', 123);
		
		self::assertEquals(123, $subject->get('a'));
	}
	
	public function test_set_Override()
	{
		$subject = new Context('a');
		
		$subject->set('a', 12);
		$subject->set('a', 123);
		
		self::assertEquals(123, $subject->get('a'));
	}
	
	public function test_set_PassArrayOfKeysForValye()
	{
		$subject = new Context('a');
		
		$subject->set(['a', 'b'], 123);
		
		self::assertEquals(123, $subject->get('a'));
		self::assertEquals(123, $subject->get('b'));
	}
	
	public function test_set_PassAssocArray()
	{
		$subject = new Context('a');
		
		$subject->set(['a' => 123, 'b' => 'c']);
		
		self::assertEquals(123, $subject->get('a'));
		self::assertEquals('c', $subject->get('b'));
	}
	
	public function test_set_OverrideParentValue()
	{
		$parent = new Context('b');
		$parent->set('b', 123);
		
		$subject = new Context('a', $parent);
		$subject->set('b', 'c');
		
		self::assertEquals('c', $subject->get('b'));
	}
	
	
	public function test_get_ValueFound_ValueReturned()
	{
		$subject = new Context('a');
		$subject->set('b', 'c');
		
		self::assertEquals('c', $subject->get('b'));
	}
	
	public function test_get_ValueFoundInParent_ValueReturned()
	{
		$parent = new Context('b');
		$parent->set('b', 123);
		
		$subject = new Context('a', $parent);
		
		self::assertEquals(123, $subject->get('b'));
	}
	
	public function test_get_ValueFoundInParentAfterContextIsCreated_ValueReturned()
	{
		$parent = new Context('b');
		$subject = new Context('a', $parent);
		
		$parent->set('b', 123);
		
		self::assertEquals(123, $subject->get('b'));
	}
	
	public function test_get_ValueFoundInParent_ValueReturnedForEachCall()
	{
		$parent = new Context('b');
		$subject = new Context('a', $parent);
		
		$parent->set('b', 123);
		
		self::assertEquals(123, $subject->get('b'));
		self::assertEquals(123, $subject->get('b'));
	}
	
	public function test_get_ValueNotFound_ExceptionThrown()
	{
		$this->expectException(\Skeleton\Exceptions\MissingContextValueException::class);
		
		$subject = new Context('a', null);
		self::assertEquals(123, $subject->get('b'));
	}
	
	public function test_get_ValueNotFoundWhenParentPresent_ExceptionThrown()
	{
		$this->expectException(\Skeleton\Exceptions\MissingContextValueException::class);
		
		$subject = new Context('a', new Context('b'));
		self::assertEquals(123, $subject->get('b'));
	}
}