<?php
namespace Skeleton\ImplementersMap;


use \Skeleton\Type;
use \Skeleton\ISingleton;


class SimpleMapTest extends \PHPUnit_Framework_TestCase 
{
	public function test_set_FirstTime() 
	{
		$map = new SimpleMap();
		$map->set('a', \stdClass::class);
	}
	
	public function test_set_Reset_NewValueReturned() 
	{
		$map = new SimpleMap();
		
		$map->set('a', 1);
		$map->set('a', 2);
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\ImplementerNotDefinedException
	 */
	public function test_get_NoImplementerDefinedForKey_ErrorIsThrown()
	{
		$map = new SimpleMap();
		$map->get("a");
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\InvalidKeyException
	 */
	public function test_get_InvalidKey_ErrorIsThrown()
	{
		$map = new SimpleMap();
		$map->get(123);
	}
	
	public function test_get_ValueExists_ValueReturned()
	{
		$map = new SimpleMap();
		$map->set('b', new \stdClass());
		$this->assertEquals(new \stdClass(), $map->get('b'));
	}
	
	public function test_get_ValueExists_SameValueReturned()
	{
		$map = new SimpleMap();
		$a = new \stdClass();
		
		$map->set('b', $a);
		$this->assertSame($a, $map->get('b'));	// First call
		$this->assertSame($a, $map->get('b'));	// Second call
	}
	
	public function test_get_ValueReturnedAccordingToKey()
	{
		$map = new SimpleMap();
		
		$map->set('a', 1);
		$map->set('b', 2);
		
		$this->assertSame(1, $map->get('a'));
		$this->assertSame(2, $map->get('b'));
	}
	
	
	/**
	 * @expectedException \Skeleton\Exceptions\InvalidKeyException
	 */
	public function test_has_KeyNotString_ErrorIsThrown()
	{
		$map = new SimpleMap();
		$map->has(12);
	}
	
	public function test_has_KeyNotDefined_ReturnFalse()
	{
		$map = new SimpleMap();
		$map->set('key-1', 'value');
		
		$this->assertFalse($map->has('key'));
	}
	
	public function test_has_KeyDefined_ReturnTrue()
	{
		$map = new SimpleMap();
		$map->set('key', 'value');
		
		$this->assertTrue($map->has('key'));
	}
}