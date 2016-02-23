<?php
namespace tests\Skeleton\ConfigLoader;


use \Skeleton\ConfigLoader\PrefixDirectoryConfigLoader;

use \tests\Skeleton\ConfigLoader\Files\LoadedNotifier;


class PrefixDirectoryConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param array $dirs
	 * @return PrefixDirectoryConfigLoader
	 */
	private function createLoader(array $dirs)
	{
		foreach ($dirs as $key => &$dir) 
		{
			$dir = __DIR__ . "/Files/PrefixDirectory/$dir";
		}
		
		return new PrefixDirectoryConfigLoader($dirs);
	}
	
	/**
	 * @param string $dir
	 * @param string $class
	 * @return string
	 */
	private function getPathToFiles($dir, $class)
	{
		return __DIR__ . "/Files/PrefixDirectory/$dir/$class.php";
	}
	
	
	protected function setUp() 
	{
		LoadedNotifier::clear();
	}
	
	
	public function test_FileNotExist_NoError()
	{
		$l = $this->createLoader(['a' => 'FileNotExist_NoError/NoConfig']);
		$l->tryLoad('a/class/name');
	}
	
	public function test_FileNotLoaded_ReturnFalse()
	{
		$l = $this->createLoader(['a' => 'FileNotExist_NoError/NoConfig']);
		$this->assertFalse($l->tryLoad('a/class/name'));
	}
	
	public function test_NoPrefixForClass_ReturnFalse()
	{
		$l = $this->createLoader(['b' => 'FileNotExist_NoError/NoConfig']);
		$this->assertFalse($l->tryLoad('a/class/name'));
	}
	
	public function test_FileLoaded_ReturnTrue()
	{
		$l = $this->createLoader(['ClassA' => 'FileLoaded_ReturnTrue/ConfigA']);
		$this->assertTrue($l->tryLoad('ClassA'));
	}
	
	public function test_FirstMatchingPrefixSelected()
	{
		$l = $this->createLoader([
			'ClassA' => 'FirstMatchingPrefixSelected/Config',
			'Clas'	 => 'FirstMatchingPrefixSelected/ConfigWrong'
		]);
		
		$l->tryLoad('ClassA');
		
		$this->assertTrue(LoadedNotifier::isLoaded(
			$this->getPathToFiles('FirstMatchingPrefixSelected/Config', 'ClassA')));
	}
	
	public function test_UnmatchingPrefixNotSelected()
	{
		$l = $this->createLoader([
			'NoMatch' => 'UnmatchingPrefixNotSelected/Config',
		]);
		
		$l->tryLoad('ClassA');
		
		$this->assertEquals(0, LoadedNotifier::getLoadedCount());
	}
	
	public function test_FileExists_FileLoaded()
	{
		$l = $this->createLoader(['FileExists_FileLoaded/ConfigA']);
		$l->tryLoad('ClassA');
		
		$this->assertTrue(LoadedNotifier::isLoaded(
			$this->getPathToFiles('FileExists_FileLoaded/ConfigA', 'ClassA')));
	}
	
	public function test_ConfigNotLoadedTwice()
	{
		$configA = 'ConfigNotLoadedTwice/ConfigA';
		
		$l = $this->createLoader([$configA]);
		$l->tryLoad('ClassA');
		$this->assertTrue(LoadedNotifier::isLoadedAt($this->getPathToFiles($configA, 'ClassA'), 1));
		LoadedNotifier::clear();
		
		$l->tryLoad('ClassA');
		
		$this->assertFalse(LoadedNotifier::isLoadedAt($this->getPathToFiles($configA, 'ClassA'), 1));
	}
	
	public function test_ComplexPath()
	{
		$configA = 'ComplexPath/ConfigA';
		
		$l = $this->createLoader([$configA]);
		$l->tryLoad('Class/In/Path');
		$this->assertTrue(LoadedNotifier::isLoaded($this->getPathToFiles($configA, 'Class/In/Path')));
	}
}