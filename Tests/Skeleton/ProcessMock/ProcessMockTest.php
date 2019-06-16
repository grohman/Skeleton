<?php
namespace Skeleton\ProcessMock;


use PHPUnit\Framework\TestCase;
use Skeleton\TestSkeleton;


class ProcessMockTest extends TestCase
{
	private function getMockFilePath(string $id): string
	{
		return realpath(__DIR__ . '/../../../Mock') . "/process_mock.$id.php";
	}
	
	private function getFileName(string $func): string
	{
		return sha1(time() . $func);
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
	
	
	public function test_addMock_ClassMocked()
	{
		$mockID		= $this->getFileName(__FUNCTION__);
		$key		= 'key_' . __FUNCTION__;
		$mock		= new ProcessMock($this->getMockFilePath($mockID));
		$className	= get_class(new class {});
		
		
		$mock->addMock($key, $className);
		TestSkeleton::includeMockFileIfExists($mockID);
		
		
		self::assertInstanceOf($className, TestSkeleton::get($key));
	}
	
	public function test_addMock_MockByValue_ValueStored()
	{
		$mockID	= $this->getFileName(__FUNCTION__);
		$key	= 'key_' . __FUNCTION__;
		$mock	= new ProcessMock($this->getMockFilePath($mockID));
		
		
		$mock->addMock($key, 'hello', true);
		TestSkeleton::includeMockFileIfExists($mockID);
		
		
		self::assertSame('hello', TestSkeleton::get($key));
	}
	
	
	public function test_addMockRaw_ValueMocked()
	{
		$mockID	= $this->getFileName(__FUNCTION__);
		$key1	= 'key1_' . __FUNCTION__;
		$key2	= 'key2_' . __FUNCTION__;
		$mock	= new ProcessMock($this->getMockFilePath($mockID));
		
		
		$mock->addMockRaw($key1, 'true', true);
		$mock->addMockRaw($key2, '2.4546', true);
		TestSkeleton::includeMockFileIfExists($mockID);
		
		
		self::assertSame(true, TestSkeleton::get($key1));
		self::assertSame(2.4546, TestSkeleton::get($key2));
	}
}