<?php
namespace Skeleton\ProcessMock;


interface IProcessMock
{
	public function addMock(string $key, string $value, bool $asValue = false): IProcessMock;
	public function addMockRaw(string $key, string $raw, bool $asValue = false): IProcessMock;
	public function addRow(string $raw): IProcessMock;
	
	public function addFile(
		string $path, 
		array $params = [],
		string $paramPrefix = ':', 
		string $paramSuffix = ''): IProcessMock;
		
	public function addTemplate(
		string $template, 
		array $params = [], 
		string $paramPrefix = ':', 
		string $paramSuffix = ''): IProcessMock;
	
	public function path(): string; 
	public function clear(): void;
}