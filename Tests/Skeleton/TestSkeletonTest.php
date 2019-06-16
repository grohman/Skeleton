<?php
namespace Skeleton;


use Skeleton\ProcessMock\IProcessMock;
use Skeleton\ProcessMock\ProcessMock;


class TestSkeletonTest extends \SkeletonTestCase
{
	private function getMockFilePath(string $id): string
	{
		return realpath(__DIR__ . '/../../Mock') . "/process_mock.$id.php";
	}
	
	
	protected function tearDown(): void
	{
		parent::tearDown();
		TestSkeleton::unset();
		
		$c = new \ReflectionClass(TestSkeleton::class);
		
		$p = $c->getProperty('processMock');
		$p->setAccessible(true);
		$p->setValue(null, null);
		
		$p = $c->getProperty('mockProcessID');
		$p->setAccessible(true);
		$p->setValue(null, null);
		
		foreach (glob(realpath(__DIR__ . '/../../Mock') . "/process_mock.*") as $file)
		{
			unlink($file);
		}
	}
	
	
	public function test_has_KeyNotFound_ReturnFalse(): void
	{
		self::assertFalse(TestSkeleton::has('a'));
	}
	
	public function test_has_KeyFound_ReturnTrue(): void
	{
		TestSkeleton::overrideValue('a', 'b');
		self::assertTrue(TestSkeleton::has('a'));
	}
	
	
	public function test_override_ValueAdded(): void
	{
		TestSkeleton::override('a', $this);
		self::assertTrue(TestSkeleton::has('a'));
	}
	
	public function test_overrideValue_ValueAdded(): void
	{
		TestSkeleton::overrideValue('a', $this);
		self::assertTrue(TestSkeleton::has('a'));
	}
	
	
	public function test_reset_ValuesReset(): void
	{
		TestSkeleton::overrideValue('a', 'b');
		TestSkeleton::reset();
		
		
		self::assertFalse(TestSkeleton::has('a'));
	}
	
	
	public function test_get_ValueNotExists_ReturnNull(): void
	{
		self::assertNull(TestSkeleton::get('a'));
	}
	
	public function test_get_ValueSetAsValue_ValueReturned(): void
	{
		$f = function () { return 123; };
		
		TestSkeleton::overrideValue('a', self::class);
		TestSkeleton::overrideValue('b', $f);
		
		self::assertEquals(self::class, TestSkeleton::get('a'));
		self::assertSame($f, TestSkeleton::get('b'));
	}
	
	public function test_get_ValueSetAsCallback_CallbackInvokedAndValueUsed(): void
	{
		$f = function () { return 123; };
		TestSkeleton::override('a', $f);
		
		self::assertEquals(123, TestSkeleton::get('a'));
	}
	
	public function test_get_ValueSetAsClassName_NewInstanceReturned(): void
	{
		$c = new class {};
		
		TestSkeleton::override('a', get_class($c));
		
		self::assertInstanceOf(get_class($c), TestSkeleton::get('a'));
		self::assertNotSame($c, TestSkeleton::get('a'));
	}
	
	public function test_get_ValueSetAsInstance_InstanceReturned(): void
	{
		TestSkeleton::override('a', $this);
		self::assertSame($this, TestSkeleton::get('a'));
	}
	
	
	public static function test_override_SkeletonsWillObtainTheValue(): void
	{
		$s = new Skeleton();
		$s->setValue('a', 'b');
		
		$c = new class {};
		
		
		TestSkeleton::override('a', get_class($c));
		
		
		self::assertInstanceOf(get_class($c), $s->get('a'));
	}
	
	public static function test_overrideValue_SkeletonsWillObtainTheValue(): void
	{
		$s = new Skeleton();
		$s->setValue('a', 'b');
		
		
		TestSkeleton::overrideValue('a', 'c');
		
		
		self::assertEquals('c', $s->get('a'));
	}
	
	
	public static function test_override_OverrideSameValue_NewValueUsed(): void
	{
		$c = new class{};
		$b = new class{};
		
		TestSkeleton::override('a', get_class($b));
		TestSkeleton::override('a', get_class($c));
		
		
		self::assertInstanceOf(get_class($c), TestSkeleton::get('a'));
		self::assertNotInstanceOf(get_class($b), TestSkeleton::get('a'));
	}
	
	public static function test_overrideValue_OverrideSameValue_NewValueUsed(): void
	{
		TestSkeleton::overrideValue('a', 'b');
		TestSkeleton::overrideValue('a', 'c');
		
		
		self::assertEquals('c', TestSkeleton::get('a'));
	}
	
	
	public function test_unset_SkeletonObjectWillReturnOriginalValue(): void
	{
		$s = new Skeleton();
		
		$s->setValue('a', 'b');
		TestSkeleton::overrideValue('a', 'c');
		
		
		TestSkeleton::unset();
		
		
		self::assertEquals('b', $s->get('a'));
	}
	
	public function test_unset_OverriddenValuesReset(): void
	{
		TestSkeleton::overrideValue('a', 'b');
		TestSkeleton::unset();
		
		
		self::assertFalse(TestSkeleton::has('a'));
	}
	
	
	public function test_includeMockFileIfExists_FileDoesNotExist_NoError(): void
	{
		TestSkeleton::includeMockFileIfExists('abcdef');
	}
	
	public function test_includeMockFileIfExists_FileExists_FileIncluded(): void
	{
		$path = sha1(time() . __FUNCTION__);
		$name = $path . __FUNCTION__;
		
		$drive = new ProcessMock($this->getMockFilePath($path));
		$drive->addRow("define('$name', true);");
		
		
		TestSkeleton::includeMockFileIfExists($path);
		
		
		self::assertTrue(defined($name));
	}
	
	public function test_includeMockFileIfExists_PassedIDUsed(): void
	{
		TestSkeleton::includeMockFileIfExists('hello');
		
		self::assertEquals('hello', TestSkeleton::getMockProcessID());
	}
	
	
	public function test_processMock_Sanity(): void
	{
		$item = TestSkeleton::processMock('hello');
		
		self::assertEquals('hello', TestSkeleton::getMockProcessID());
		self::assertInstanceOf(IProcessMock::class, $item);
	}
}