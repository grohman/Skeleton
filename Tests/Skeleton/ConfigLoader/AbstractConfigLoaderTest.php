<?php
namespace Skeleton\ConfigLoader;


use PHPUnit\Framework\TestCase;
use Skeleton\Base\IBoneConstructor;
use Skeleton\Type;


class AbstractConfigLoaderHelper extends AbstractConfigLoader
{
	/**
	 * @param string $path
	 * @return bool
	 */
	public function tryLoad($path)
	{
		return true;
	}
}


class AbstractConfigLoaderTest extends TestCase
{
	public function test_sanity()
	{
		/** @var IBoneConstructor $constructor */
		$constructor = new class implements IBoneConstructor
		{
			public $data = [];
			
			public function set($key, $value, int $flags = Type::Instance): IBoneConstructor
			{
				$this->data[] = [$key, $value, $flags];
				
				/** @var IBoneConstructor $this */
				return $this;
			}
			
			public function setValue(string $key, $value): IBoneConstructor
			{
				return $this->set($key, $value, Type::ByValue);
			}
		};
		
		$subject = new AbstractConfigLoaderHelper();
		
		
		self::assertSame($subject, $subject->setBoneConstructor($constructor));
		self::assertSame($constructor, $subject->set('a', 'b', 2));
		
		/** @noinspection PhpUndefinedFieldInspection */
		self::assertEquals([['a', 'b', 2]], $constructor->data);
	}
}