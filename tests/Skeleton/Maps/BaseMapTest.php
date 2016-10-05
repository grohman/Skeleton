<?php
namespace Skeleton\Maps;


use Skeleton\Base\ILoader;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Loader\ValueLoader;
use Skeleton\Tools\Knot\Knot;
use Skeleton\Type;


class BaseMapTest extends \PHPUnit_Framework_TestCase
{
	public function test_constructor_loaderNotPassed_NewLoaderCreated()
	{
		$this->assertInstanceOf(ValueLoader::class, (new BaseMapTest_BaseMap_Helper())->loader());
	}
	
	public function test_constructor_loaderPassed_PassedLoaderUsed()
	{
		/** @var ILoader $loader */
		$loader = $this->getMock(ILoader::class);
		
		$this->assertSame($loader, (new BaseMapTest_BaseMap_Helper($loader))->loader());
	}
	
	
	public function test_setLoader()
	{
		/** @var ILoader $loader */
		$loader = $this->getMock(ILoader::class);
		$map = new BaseMapTest_BaseMap_Helper();
		$map->setLoader($loader);
		
		$this->assertSame($loader, $map->loader());
	}
	
	public function test_enableKnot_EnableKnotOnLoaderCalled()
	{
		/** @var ISkeletonSource $s */
		$s = $this->getMock(ISkeletonSource::class);
		$l = $this->getMock(ILoader::class);
		
		$l->expects($this->once())
			->method('setKnot')
			->with($this->isInstanceOf(Knot::class));
		
		$m = new BaseMapTest_BaseMap_Helper($l);
		$m->enableKnot($s);
	}
}


class BaseMapTest_BaseMap_Helper extends BaseMap
{
	public function set($key, $value, $flags = Type::Instance) {}
	public function forceSet($key, $value, $flags = Type::Instance) {}
	public function get($key) {}
	public function has($key) {}
}