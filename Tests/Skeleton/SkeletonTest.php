<?php
namespace Skeleton;


use PHPUnit\Framework\MockObject\MockObject;

use Skeleton\Base\IContextReference;
use Skeleton\Base\IMap;
use Skeleton\Base\ILoader;
use Skeleton\Base\IConfigLoader;

use Skeleton\Maps\SimpleMap;
use Skeleton\Tools\ContextManager;


class SkeletonTest extends \SkeletonTestCase
{
	/**
	 * @param Skeleton $s
	 * @return MockObject|IMap
	 */
	private function mockMap(Skeleton $s) 
	{
		/** @var MockObject|IMap $map */
		$map = $this->getMock(IMap::class);
		$map->method('loader')->willReturn($this->mockLoader());
		$s->setMap($map);
		return $map;
	}
	
	/**
	 * @return MockObject|ILoader
	 */
	private function mockLoader()
	{
		return $this->getMock(ILoader::class);
	}
	
	/**
	 * @return MockObject|GlobalSkeleton
	 */
	private function mockGlobalSkeleton()
	{
		$global = $this->getMockBuilder(GlobalSkeleton::class)
			->disableOriginalConstructor()
			->getMock();
		
		$ref = new \ReflectionClass(GlobalSkeleton::class);
		$instanceProperty = $ref->getProperty('instance');
		$instanceProperty->setAccessible(true);
		$instanceProperty->setValue(null, $global);
		
		return $global;
	}
	
	/**
	 * @param Skeleton $s
	 * @param bool $has
	 * @return MockObject
	 */
	private function mockMapHasValue(Skeleton $s, $has = false) 
	{
		$map = $this->mockMap($s);
		$map->method('has')->willReturn($has);
		return $map;
	}
	
	/**
	 * @param Skeleton $s
	 * @return MockObject|IConfigLoader
	 */
	private function mockConfigLoader(Skeleton $s) 
	{
		/** @var MockObject|IConfigLoader $loader */
		$loader = $this->getMock(IConfigLoader::class);
		$s->setConfigLoader($loader);
		return $loader;
	}
	
	
	protected function tearDown(): void
	{
		$ref = new \ReflectionClass(GlobalSkeleton::class);
		$instanceProperty = $ref->getProperty('instance');
		$instanceProperty->setAccessible(true);
		$instanceProperty->setValue(null, null);
	}
	
	
	public function test_SelfReturned() 
	{
		$s = new Skeleton();
		
		/** @var MockObject|IMap $map */
		$map = $this->getMock(IMap::class);
		
		/** @var MockObject|IConfigLoader $loader */
		$loader = $this->getMock(IConfigLoader::class);
		
		$this->assertSame($s, $s->setMap($map));
		$this->assertSame($s, $s->setConfigLoader($loader));
		$this->assertSame($s, $s->set('a', 'b'));
		$this->assertSame($s, $s->enableKnot());
	}
	
	public function test_get_KeyIsNotString_ErrorThrown()
	{
		$this->expectException(\Skeleton\Exceptions\InvalidKeyException::class);
		
		$s = new Skeleton();
		$s->get(12);
	}
	
	public function test_get_MapAlreadyHasClass_GetOnMapCalled()
	{
		$s = new Skeleton();
		$this->mockMapHasValue($s, true);
		$loader = $this->mockConfigLoader($s);
		
		$loader->expects($this->never())->method('tryLoad');
		
		$s->get('a\b');
	}
	
	/**
	 * Ignore missing value, this is not tested by this unit test.
	 */
	public function test_get_NotClassInMap_ConfigLoaderCalled()
	{
		$this->expectException(\Skeleton\Exceptions\ImplementerNotDefinedException::class);
		
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockConfigLoader($s);
		
		$loader->expects($this->atLeastOnce())->method('tryLoad');
		
		$s->get('a\b');
	}
	
	/**
	 * Ignore missing value, this is not tested by this unit test.
	 */
	public function test_get_ConfigLoaderCalledWithCorrectValues()
	{
		$this->expectException(\Skeleton\Exceptions\ImplementerNotDefinedException::class);
		
		$s = new Skeleton();
		$this->mockMapHasValue($s, false);
		$loader = $this->mockConfigLoader($s);
		
		$loader->expects($this->once())->method('tryLoad')->with('a');
		
		$s->get('a\b');
	}
	
	public function test_get_ValueExistsAfterConfigLoaderIsUsed_LoadedValueReturned()
	{
		$s = new Skeleton();
		$loader = $this->mockConfigLoader($s);
		$s->setMap(new SimpleMap());
		
		$loader
			->expects($this->once())
			->method('tryLoad')
			->willReturnCallback(
				function() 
					use ($s) 
				{
					$s->set('some\complex\namespace', 123);
					return true;
				}
			);
		
		$this->assertSame(123, $s->get('some\complex\namespace'));
	}
	
	public function test_get_PassContext_ContextUsed()
	{
		/**
		 * @autoload
		 */
		$cls = new class 
		{
			/** @context */
			public $item;
		};
		
		$s = new Skeleton();
		$s->enableKnot();
		$s->set('a', get_class($cls));
		
		/** @var mixed $obj */
		$obj = $s->get('a', ['item' => 123]);
		
		self::assertEquals(123, $obj->item);
	}
	
	
	/**
	 * Ignore missing value, this is not tested by this unit test.
	 */
	public function test_get_SkeletonUseGlobalFlagNotSet_GlobalNotCalled()
	{
		$this->expectException(\Skeleton\Exceptions\ImplementerNotDefinedException::class);
		
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		
		$global->expects($this->never())->method('get');
		
		$s->get('a');
	}
	
	public function test_get_SkeletonUseGlobalFlagSet_GlobalSkeletonCalled()
	{
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		
		$global->expects($this->once())->method('get');
		
		$s->useGlobal();
		
		$s->get('a');
	}
	
	public function test_get_SkeletonUseGlobalFlagSet_KeyPassedToGlobalSkeleton()
	{
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		
		$global->expects($this->once())->method('get')->with('a');
		
		$s->useGlobal();
		
		$s->get('a');
	}
	
	/**
	 * Ignore missing value, this is not tested by this unit test.
	 */
	public function test_get_FunctionUseGlobalFlagIsFalse_GlobalNotCalled()
	{
		$this->expectException(\Skeleton\Exceptions\ImplementerNotDefinedException::class);
		
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		$s->useGlobal();
		
		$global->expects($this->never())->method('get');
		
		$s->get('a', null, true);
	}
	
	public function test_get_ObjectReturnedFromGlobal_NoErrorThrown()
	{
		$global = $this->mockGlobalSkeleton();
		$s = new Skeleton();
		$s->useGlobal();
		
		$global->method('get')->willReturn(123);
		
		$this->assertSame(123, $s->get('a'));
	}
	
	public function test_set_KeyIsNotString_ErrorThrown()
	{
		$this->expectException(\Skeleton\Exceptions\InvalidKeyException::class);
		
		$s = new Skeleton();
		$s->set(12, "a");
	}
	
	public function test_set_ReturnsSelf()
	{
		$s = new Skeleton();
		$this->assertSame($s, $s->set('a', 'b'));
	}
	
	public function test_set_SetOnMapCalled()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$map->expects($this->once())
			->method('set')
			->with('a', 'b', Type::ByValue);
		
		$s->set('a', 'b', Type::ByValue);
	}
	
	public function test_set_KeyIsArray_AllValuesAdded()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$map->expects($this->at(0))
			->method('set')
			->with('a', 'val', Type::ByValue);
		
		$map->expects($this->at(1))
			->method('set')
			->with('b', 'val', Type::ByValue);
		
		$s->set(['a', 'b'], 'val', Type::ByValue);
	}
	
	public function test_set_KeyIsArray_SelfReturned()
	{
		$s = new Skeleton();
		$this->assertSame($s, $s->set(['a', 'b'], 'val'));
	}
	
	
	public function test_setValue_SkeletonReturned()
	{
		$s = new Skeleton();
		$this->assertSame($s, $s->setValue('a', 'b'));
	}
	
	public function test_setValue_ObjectSetByValue()
	{
		$s = new Skeleton();
		$s->setValue('a', self::class);
		
		self::assertEquals(self::class, $s->get('a'));
	}
	
	
	public function test_override_ForceSetCalledOnMap()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$map->expects($this->once())
			->method('forceSet')
			->with('a', 'b', Type::ByValue);
		
		$s->override('a', 'b', Type::ByValue);
	}
	
	
	public function test_registerGlobalFor()
	{
		$now = time();
		
		$sMain = new Skeleton();
		$sMain->set('hello\world', $now);
		$sMain->registerGlobalFor('hello');
		
		$sSec = new Skeleton();
		$sSec->useGlobal();
		
		
		self::assertEquals($now, $sSec->get('hello\world'));
	}
	
	
	public function test_setMap_MapSet() 
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$this->assertSame($map, $s->getMap());
	}
	
	
	public function test_setConfigLoader_LoaderSet() 
	{
		$s = new Skeleton();
		$loader = $this->mockConfigLoader($s);
		
		$this->assertSame($loader, $s->getConfigLoader());
	}
	
	public function test_setConfigLoader_SetToNull() 
	{
		$s = new Skeleton();
		$s->setConfigLoader(null);
		$this->assertNull($s->getConfigLoader());
	}
	
	public function test_setConfigLoader_PassNamespaceLimit_KeysOutsideLimitAreNotLoaded()
	{
		$this->expectException(\Skeleton\Exceptions\ImplementerNotDefinedException::class);
		
		/** @var MockObject|IConfigLoader $loader */
		$loader = $this->getMock(IConfigLoader::class);
		
		$s = new Skeleton();
		$s->setConfigLoader($loader, 'ABC');
		
		
		$loader
			->expects($this->never())
			->method('tryLoad');
		
		
		$s->get('Hello\World');
	}
	
	public function test_setConfigLoader_PassNamespaceLimit_KeysInsideLimitPassedToLoader() 
	{
		/** @var MockObject|IConfigLoader $loader */
		$loader = $this->getMock(IConfigLoader::class);
		
		$s = new Skeleton();
		$s->setConfigLoader($loader, 'ABC');
		
		
		$loader
			->expects($this->once())
			->method('tryLoad')
			->willReturnCallback(
				function () 
					use ($s)
				{
					$s->setValue('ABC\World', 123);
					return true;
				}
			);
		
		
		self::assertEquals(123, $s->get('ABC\World'));
	}
	
	public function test_setConfigLoader_PassNamespaceLimit_KeyIsShorterThenLimit()
	{
		$this->expectException(\Skeleton\Exceptions\ImplementerNotDefinedException::class);
		
		/** @var MockObject|IConfigLoader $loader */
		$loader = $this->getMock(IConfigLoader::class);
		
		$s = new Skeleton();
		$s->setConfigLoader($loader, 'ABC');
		
		
		$loader
			->expects($this->never())
			->method('tryLoad');
		
		
		$s->get('AB');
	}
	
	
	public function test_enableKnot_EnableKnotOnMapCalled()
	{
		$s = new Skeleton();
		
		$map = $this->mockMap($s);
		$map->expects($this->once())
			->method('enableKnot')
			->with($s);
		
		$s->enableKnot();
	}
	
	
	public function test_load_LoadCalledOnMapsLoader()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		/** @var MockObject $loader */
		$loader = $map->loader();
		$loader->expects($this->once())->method('get')->with('abc')->willReturn(123);
		
		$this->assertEquals(123, $s->load('abc'));
	}
	
	public function test_load_ContextNotPassed_PassNullToLoader()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		/** @var MockObject $loader */
		$loader = $map->loader();
		$loader->expects($this->once())->method('get')->with($this->anything(), null);
		
		$s->load(\stdClass::class);
	}
	
	public function test_load_IContextReferencePassed_IContextReferencePassedToLoader()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$context = new ContextReference(new Context(), $s);
		
		/** @var MockObject $loader */
		$loader = $map->loader();
		$loader->expects($this->once())->method('get')->with($this->anything(), $context);
		
		$s->load(\stdClass::class, $context);
	}
	
	public function test_load_ContextPassed_IContextReferenceWithContextPassed()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		$context = new Context();
		
		/** @var MockObject $loader */
		$loader = $map->loader();
		$loader->expects($this->once())
			->method('get')
			->willReturnCallback(function ($a, IContextReference $b) use ($context)
			{
				self::assertEquals($context, $b->context());
			});
		
		$s->load(\stdClass::class, $context);
	}
	
	public function test_load_ArrayPassed_IContextReferenceWithArrayDataPassed()
	{
		$s = new Skeleton();
		$map = $this->mockMap($s);
		
		/** @var MockObject $loader */
		$loader = $map->loader();
		$loader->expects($this->once())
			->method('get')
			->willReturnCallback(function ($a, IContextReference $b)
			{
				self::assertEquals('b', $b->context()->get('a'));
			});
		
		$s->load(\stdClass::class, ['a' => 'b']);
	}
	
	
	public function test_for_InstancePassed_InstanceContextReferenceReturned()
	{
		$s = new Skeleton();
		
		$obj = new \stdClass();
		$s->context($obj);
		
		self::assertEquals($obj->{ContextManager::CONTEXT_PROPERTY_NAME}, $s->for($obj));
	}
	
	public function test_for_ArrayPassed_InstanceContextForArrayReturned()
	{
		$s = new Skeleton();
		
		$result = $s->for(['a' => 'b']);
		
		self::assertInstanceOf(IContextReference::class, $result);
		self::assertEquals('b', $result->context()->get('a'));
	}
	
	
	public function test_context_Sanity(): void
	{
		$s = new Skeleton();
		
		
		self::assertInstanceOf(Context::class, $s->context(['a' => 'b']));
		
		self::assertEquals('b', $s->context(['a' => 'b'])->get('a'));
		self::assertEquals('c', $s->context(['a' => 'b'], 'c')->name());
	}
	
	
	public function test_create_NewSkeletonObjectReturned(): void
	{
		self::assertInstanceOf(Skeleton::class, Skeleton::create(__NAMESPACE__));
	}
	
	public function test_create_KnotEnabled(): void
	{
		$s = Skeleton::create(__NAMESPACE__);
		$s->set(SkeletonTest_Helper_A::class, SkeletonTest_Helper_A::class);
		$i = $s->load(SkeletonTest_Helper_B::class);
		
		self::assertInstanceOf(SkeletonTest_Helper_A::class, $i->a);
	}
	
	public function test_create_RegisteredAsGlobal(): void
	{
		$key = __FUNCTION__ . '\\' . SkeletonTest_Helper_A::class;
		$s = Skeleton::create(__FUNCTION__);
		$s->set($key, SkeletonTest_Helper_A::class);
		
		$thirdPartySkeleton = new Skeleton();
		$thirdPartySkeleton->useGlobal();
		
		
		$i = $thirdPartySkeleton->get($key);
		
		
		self::assertInstanceOf(SkeletonTest_Helper_A::class, $i);
	}
	
	public function test_create_GlobalScopeUsed(): void
	{
		$key = __FUNCTION__ . '\\' . SkeletonTest_Helper_A::class;
		$thirdPartySkeleton = new Skeleton();
		$thirdPartySkeleton->registerGlobalFor(__FUNCTION__);
		$thirdPartySkeleton->set($key, SkeletonTest_Helper_A::class);
		
		$s = Skeleton::create(__NAMESPACE__);
		
		
		$i = $s->get($key);
		
		
		self::assertInstanceOf(SkeletonTest_Helper_A::class, $i);
	}
	
	public function test_create_ConfigLoaderIsSetup(): void
	{
		$key = 'ConfigLoaderIsSetup\\ABC';
		$s = Skeleton::create('ConfigLoaderIsSetup', realpath(__DIR__ . '/SkeletonTest'));
		
		self::assertEquals($s->get($key), '12345');
	}
	
	public function test_create_ConfigLoaderIsSetupAndLimitedToNamespace(): void
	{
		$this->expectException(\Skeleton\Exceptions\ImplementerNotDefinedException::class);
		
		$keySuccess = 'ConfigLoaderIsSetupAndLimited\\ABC';
		$keyFail = 'Fail_ConfigLoaderIsSetupAndLimited\\ABC';
		$s = Skeleton::create('ConfigLoaderIsSetupAndLimited', realpath(__DIR__ . '/SkeletonTest'));
		
		
		self::assertEquals('12345', $s->get($keySuccess));
		$s->get($keyFail);
	}
	
	
	public function test_container_PassInterface_InterfaceLoaded(): void
	{
		$s = new Skeleton();
		$s->registerGlobalFor('abc');
		$s->set('abc/def', 123);
		
		self::assertEquals(123, Skeleton::container('abc/def'));
	}
	
	public function test_container_PassClassName_InstanceLoaded(): void
	{
		$s = new Skeleton();
		$s->registerGlobalFor('Skeleton');
		$s->enableKnot();
		$s->set(SkeletonTest_Container_Helper_A::class, SkeletonTest_Container_Helper_A::class);
		
		
		$item = Skeleton::container(SkeletonTest_Container_Helper_B::class);
		
		
		self::assertInstanceOf(SkeletonTest_Container_Helper_B::class, $item);
		self::assertInstanceOf(SkeletonTest_Container_Helper_A::class, $item->a);
	}
	
	public function test_container_PassInstance_InstanceLoaded(): void
	{
		$s = new Skeleton();
		$s->registerGlobalFor('Skeleton');
		$s->enableKnot();
		$s->set(SkeletonTest_Container_Helper_A::class, SkeletonTest_Container_Helper_A::class);
		
		
		$item = new SkeletonTest_Container_Helper_B();
		Skeleton::container($item);
		
		
		self::assertInstanceOf(SkeletonTest_Container_Helper_B::class, $item);
		self::assertInstanceOf(SkeletonTest_Container_Helper_A::class, $item->a);
	}
	
	
	public function testSanity_knotNotEnabled(): void
	{
		$s = new Skeleton();
		
		$s->set(SkeletonTest_Helper_A::class, SkeletonTest_Helper_A::class);
		$s->set(SkeletonTest_Helper_B::class, SkeletonTest_Helper_B::class);
		
		/** @var SkeletonTest_Helper_B $a */
		$a = $s->get(SkeletonTest_Helper_B::class);
		
		$this->assertNull($a->a);
	}
	
	
	public function test_GlobalMethod_skeleton_PassInterface_InterfaceLoaded(): void
	{
		$s = new Skeleton();
		$s->registerGlobalFor('abc');
		$s->set('abc/def', 123);
		
		self::assertEquals(123, skeleton('abc/def'));
	}
	
	public function test_GlobalMethod_skeleton_PassClassName_InstanceLoaded(): void
	{
		$s = new Skeleton();
		$s->registerGlobalFor('Skeleton');
		$s->enableKnot();
		$s->set(SkeletonTest_Container_Helper_A::class, SkeletonTest_Container_Helper_A::class);
		
		
		$item = skeleton(SkeletonTest_Container_Helper_B::class);
		
		
		self::assertInstanceOf(SkeletonTest_Container_Helper_B::class, $item);
		self::assertInstanceOf(SkeletonTest_Container_Helper_A::class, $item->a);
	}
	
	public function test_GlobalMethod_skeleton_PassInstance_InstanceLoaded(): void
	{
		$s = new Skeleton();
		$s->registerGlobalFor('Skeleton');
		$s->enableKnot();
		$s->set(SkeletonTest_Container_Helper_A::class, SkeletonTest_Container_Helper_A::class);
		
		
		$item = new SkeletonTest_Container_Helper_B();
		skeleton($item);
		
		
		self::assertInstanceOf(SkeletonTest_Container_Helper_B::class, $item);
		self::assertInstanceOf(SkeletonTest_Container_Helper_A::class, $item->a);
	}
	
	
	public function test_LoadByPropertyType(): void 
	{
		$s = new Skeleton();
		$s->enableKnot();
		
		
		$s->set(SkeletonTest_Container_Helper_A::class, SkeletonTest_Container_Helper_A::class);
		$s->set(SkeletonTest_Container_Helper_Extended_A::class, SkeletonTest_Container_Helper_A::class);
		
		
		$item = $s->load(SkeletonTest_ByPropertyType::class);
		self::assertInstanceOf(SkeletonTest_Container_Helper_A::class, $item->a);
	}
	
	public function test_PreferAutoloadValueOverType(): void 
	{
		$s = new Skeleton();
		$s->enableKnot();
		
		
		$s->set(SkeletonTest_Container_Helper_A::class, SkeletonTest_Container_Helper_A::class);
		$s->set(SkeletonTest_Container_Helper_Extended_A::class, SkeletonTest_Container_Helper_Extended_A::class);
		
		
		$item = $s->load(SkeletonTest_ByPropertyType::class);
		self::assertInstanceOf(SkeletonTest_Container_Helper_Extended_A::class, $item->b);
	}
}


class SkeletonTest_Helper_A {}


/**
 * @autoload
 */
class SkeletonTest_Helper_B
{
	/**
	 * @autoload
	 * @var SkeletonTest_Helper_A
	 */
	public $a;
}


/**
 * @autoload
 */
class SkeletonTest_Container_Helper_A
{
	
}

/**
 * @autoload
 */
class SkeletonTest_Container_Helper_B
{
	/**
	 * @autoload
	 * @var \Skeleton\SkeletonTest_Container_Helper_A
	 */
	public $a;
}

class SkeletonTest_Container_Helper_Extended_A extends SkeletonTest_Container_Helper_A
{
	
}


/**
 * @autoload
 */
class SkeletonTest_ByPropertyType
{
	/**
	 * @autoload
	 */
	public SkeletonTest_Container_Helper_A $a;
	
	/**
	 * @autoload
	 * @var \Skeleton\SkeletonTest_Container_Helper_Extended_A
	 */
	public SkeletonTest_Container_Helper_A $b;
}