<?php
namespace Skeleton\ProcessMock;


use Skeleton\Exceptions\ProcessMockException;


class ProcessMock implements IProcessMock
{
	private $path;
	private $isMocked = false;
	
	/** @var MockFileDrive */
	private $driver;
	
	
	private function lockForMocking(): void
	{
		if (!$this->driver->isLockedByDriver())
		{
			$this->driver->delete();
			$this->driver->create();
			$this->driver->lock();
			$this->addFile(__DIR__ . '/Content/original_file.php');
		}
	}
	
	private function getMockMethod(bool $asValue): string
	{
		if ($asValue)
		{
			return 'mock_value';
		}
		else 
		{
			return 'mock';
		}
	}
	
	
	public function __construct(string $path)
	{
		$this->path = $path;
		$this->driver = new MockFileDrive($path);
	}
	
	public function __destruct()
	{
		if ($this->driver->isLockedByDriver())
		{
			$this->driver->delete();
		}
	}
	
	
	public function addMock(string $key, string $value, bool $asValue = false): IProcessMock
	{
		$method = $this->getMockMethod($asValue);
		return $this->addTemplate("$method(':key', ':value');", ['key' => $key, 'value' => $value]);
	}
	
	public function addMockRaw(string $key, string $raw, bool $asValue = false): IProcessMock
	{
		$method = $this->getMockMethod($asValue);
		return $this->addTemplate("$method(':key', :raw);", ['key' => $key, 'raw' => $raw]);
	}
	
	public function addRow(string $raw): IProcessMock
	{
		if ($this->isMocked)
			throw new ProcessMockException('Can not modify the mock file from a mocked process');
		
		$this->lockForMocking();
		$this->driver->append($raw);
		
		return $this;
	}
	
	public function addFile(
		string $path, 
		array $params = [],
		string $paramPrefix = ':', 
		string $paramSuffix = ''): IProcessMock
	{
		if (!file_exists($path) || !is_readable($path))
			throw new ProcessMockException("The file '$path' does not exist or is unreadable");
		
		$content = file_get_contents($path);
		
		if ($content === false)
			throw new ProcessMockException("Failed to read file '$path'");
		
		return $this->addTemplate($content, $params, $paramPrefix, $paramSuffix);
	}
	
	public function addTemplate(
		string $template, 
		array $params = [], 
		string $paramPrefix = ':', 
		string $paramSuffix = ''): IProcessMock
	{
		foreach ($params as $name => $value)
		{
			$template = str_replace(
				$paramPrefix . $name . $paramSuffix,
				$value,
				$template
			);
		}
		
		return $this->addRow($template);
	}
	
	public function path(): string
	{
		return $this->path;
	}
	
	public function clear(): void
	{
		$this->driver->delete();
	}
	
	
	public function includeIfExists(): bool
	{
		if ($this->isMocked)
		{
			throw new ProcessMockException(
				'Include for the mock file should not be called more then once', 
				$this->path());
		}
		
		if (!$this->driver->isExists())
			return false;
		
		if (!$this->driver->isLocked())
		{
			// An exception can be thrown here if a parallel process is running and it deletes this file
			// in a race condition. In this case, rerunning the current process should not fail again.
			$this->driver->delete();
			return false;
		}
		
		$this->isMocked = true;
		
		/** @noinspection PhpIncludeInspection */
		require_once $this->path();
		
		return true;
	}
	
	public function isCurrentProcessMocked(): bool
	{
		return $this->isMocked;
	}
}