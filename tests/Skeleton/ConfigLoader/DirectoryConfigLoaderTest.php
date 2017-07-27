<?php
namespace Skeleton\ConfigLoader;


use tests\Skeleton\ConfigLoader\Files\LoadedNotifier;


class DirectoryConfigLoaderTest extends \SkeletonTestCase
{
	/**
	 * @param array $dirs
	 * @return DirectoryConfigLoader
	 */
	private function createLoader(array $dirs)
	{
		foreach ($dirs as &$dir) 
		{
			$dir = __DIR__ . "/Files/Directory/$dir";
		}
		
		if (count($dirs) == 1) 
		{
			return new DirectoryConfigLoader($dirs[0]);
		}
		else
		{
			return new DirectoryConfigLoader($dirs);
		}
	}
	
	/**
	 * @param string $dir
	 * @param string $class
	 * @return string
	 */
	private function getPathToFiles($dir, $class)
	{
		return __DIR__ . "/Files/Directory/$dir/$class.php";
	}
	
	
	protected function setUp() 
	{
		LoadedNotifier::clear();
	}
	
	
	public function test_FileNotExist_NoError()
	{
		$l = $this->createLoader(['FileNotExist_NoError/NoConfig']);
		$l->tryLoad('NotFound');
	}
	
	public function test_FileNotLoaded_ReturnFalse()
	{
		$l = $this->createLoader(['FileNotExist_NoError/NoConfig']);
		$this->assertFalse($l->tryLoad('NotFound'));
	}
	
	public function test_FileLoaded_ReturnTrue()
	{
		$l = $this->createLoader(['FileLoaded_ReturnTrue/ConfigA']);
		$this->assertTrue($l->tryLoad('ClassA'));
	}
	
	public function test_FileLoadedInOneConfigOnly_ReturnTrue()
	{
		$configA = 'FileLoadedInOneConfigOnly_ReturnTrue/ConfigA';
		$configB = 'FileLoadedInOneConfigOnly_ReturnTrue/ConfigB';
		$configC = 'FileLoadedInOneConfigOnly_ReturnTrue/ConfigC';
		
		$l = $this->createLoader([$configA, $configB, $configC]);
		
		$this->assertTrue($l->tryLoad('ClassA'));
	}
	
	public function test_FileExists_FileLoaded()
	{
		$l = $this->createLoader(['FileExists_FileLoaded/ConfigA']);
		$l->tryLoad('ClassA');
		
		$this->assertTrue(LoadedNotifier::isLoaded(
			$this->getPathToFiles('FileExists_FileLoaded/ConfigA', 'ClassA')));
	}
	
	public function test_FileExistsInNumberOfDirectories_AllLoaded()
	{
		$configA = 'FileExistsInNumberOfDirectories_AllLoaded/ConfigA';
		$configB = 'FileExistsInNumberOfDirectories_AllLoaded/ConfigB';
		
		$l = $this->createLoader([$configA, $configB]);
		
		$l->tryLoad('ClassA');
		
		$this->assertTrue(LoadedNotifier::isLoaded($this->getPathToFiles($configA, 'ClassA')));
		$this->assertTrue(LoadedNotifier::isLoaded($this->getPathToFiles($configB, 'ClassA')));
	}
	
	public function test_FileExistsInNumberOfDirectories_LoadOrderIsCorrect()
	{
		$configA = 'FileExistsInNumberOfDirectories_LoadOrderIsCorrect/ConfigA';
		$configB = 'FileExistsInNumberOfDirectories_LoadOrderIsCorrect/ConfigB';
		
		$l = $this->createLoader([$configB, $configA]);
		$l->tryLoad('ClassA');
		
		$this->assertTrue(LoadedNotifier::isLoadedAt($this->getPathToFiles($configB, 'ClassA'), 1));
		$this->assertTrue(LoadedNotifier::isLoadedAt($this->getPathToFiles($configA, 'ClassA'), 2));
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