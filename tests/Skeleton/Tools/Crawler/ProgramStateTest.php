<?php
namespace Skeleton\Tools\Crawler;


class ProgramStateTest extends \PHPUnit_Framework_TestCase
{
	public function test_getNewDeclarations_NoNewDeclarations_ReturnEmptyArray()
	{
		$ps = new ProgramState();
		$ps->saveState();
		
		$this->assertEquals([], $ps->getNewDeclarations());
	}
	
	public function test_getNewDeclarations_HasNewClass_DeclarationsReturned()
	{
		$ps = new ProgramState();
		$ps->saveState();
		
		$clsName = __FUNCTION__;
		eval("class $clsName {}");
		
		$this->assertEquals([__FUNCTION__], $ps->getNewDeclarations());
	}
	
	public function test_getNewDeclarations_HasNewInterface_DeclarationsReturned()
	{
		$ps = new ProgramState();
		$ps->saveState();
		
		$clsName = __FUNCTION__;
		eval("interface $clsName {}");
		
		$this->assertEquals([__FUNCTION__], $ps->getNewDeclarations());
	}
	
	public function test_getNewDeclaration_HasNumberOfNewDeclarations()
	{
		$ps = new ProgramState();
		$ps->saveState();
		
		$clsName = __FUNCTION__;
		eval("class $clsName {}");
		eval("interface I$clsName {}");
		
		$this->assertEquals([__FUNCTION__, 'I' . __FUNCTION__], $ps->getNewDeclarations());
	}
}
