<?php
namespace Skeleton;


use Skeleton\Maps\TestMap;


class some_module  
{
	use TModule;
	
	protected function skeleton() { return null; }
	protected function getComponent() { return []; }
}

/**
 * @method static string a()
 * @method static string bb()
 * @method static string mod_a()
 */
class TModuleTestHelper 
{
	use TModule;
	
	/** @var Skeleton */
	private $skeleton = null;
	
	
	/**
	 * @return Skeleton
	 */
	protected function skeleton()
	{
		if (!$this->skeleton)
		{
			$this->skeleton = new Skeleton();
			
			$map = new TestMap($this->skeleton->getMap());
			$this->skeleton->setMap($map);
			
			$map->override('a', 'a_val');
			$map->override('b', 'b_val');
		}
		
		return $this->skeleton;
	}
	
	protected function getSubModules()
	{
		return [
			'mod_a'	=> some_module::class
		];
	}
	
	/**
	 * @return array
	 */
	protected function getComponent()
	{
		return [
			'a'		=> 'a',
			'bb'	=> 'b'
		];
	}
}



class TModuleTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Skeleton\Exceptions\SkeletonException
	 */
	public function test_get_NotFound_ErrorThrown()
	{
		/** @noinspection PhpUndefinedMethodInspection */
		TModuleTestHelper::aa();
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\SkeletonException
	 */
	public function test_instance_get_NotFound_ErrorThrown()
	{
		/** @noinspection PhpUndefinedMethodInspection */
		TModuleTestHelper::instance()->aa();
	}
	
	
	public function test_get_ElementFound_ElementReturned()
	{
		$this->assertEquals('b_val', TModuleTestHelper::bb());
	}
	
	public function test_get_instance_ElementFound_ElementReturned()
	{
		$this->assertEquals('b_val', TModuleTestHelper::instance()->bb());
	}
	
	public function test_get_SubModuleFound_ElementReturned()
	{
		$this->assertInstanceOf(some_module::class, TModuleTestHelper::mod_a());
	}
	
	public function test_get_instance_SubModuleFound_ElementReturned()
	{
		$this->assertInstanceOf(some_module::class, TModuleTestHelper::instance()->mod_a());
	}
}
