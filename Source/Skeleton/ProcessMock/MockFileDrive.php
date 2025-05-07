<?php
namespace Skeleton\ProcessMock;


use Skeleton\Exceptions\ProcessMockException;


class MockFileDrive
{
	private $path;
	
	private $isLocked = false;
	
	/** @var resource */
	private $resource = null;
	
	
	public function __construct(string $path)
	{
		$this->path = $path;
	}
	
	
	public function create(): void
	{
		if ($this->isExists())
			throw new ProcessMockException('File already exists', $this->path);
		
		$resource = fopen($this->path, 'w');
		
		if (!$resource)
			throw new ProcessMockException('Failed to create file', $this->path);
		
		$this->resource = $resource;
	}
	
	public function delete(): void
	{
		if (!$this->isExists())
			return;
		
		if (!$this->isLocked && $this->isLocked())
		{
			throw new ProcessMockException('Can not delete file locked by another process', $this->path);
		}
		
		$this->isLocked = false;
		
		if ($this->resource)
		{
			fclose($this->resource);
			$this->resource = null;
		}
		
		if (!unlink($this->path))
		{
			throw new ProcessMockException('Failed to delete file', $this->path);
		}
	}
	
	public function isExists(): bool
	{
		return file_exists($this->path);
	}
	
	public function isLocked(): bool
	{
		if (!$this->isExists())
			return false;
		
		if ($this->resource)
		{
			return $this->isLocked;
		}
		
		$resource = null;
		
		try
		{
			$resource = fopen($this->path, 'r');
			
			if (!$resource)
				return false;
			
			$canLock = flock($resource, LOCK_EX | LOCK_NB);
			
			return !$canLock;
		}
		finally
		{
			if ($resource)
			{
				fclose($resource);
			}
		}
	}
	
	public function isLockedByDriver(): bool
	{
		return $this->isLocked;	
	}
	
	public function isOpen(): bool
	{
		return (bool)$this->resource;
	}
	
	public function lock(): void
	{
		if (!$this->isExists())
			throw new ProcessMockException('File does not exist', $this->path);
		
		if (!$this->resource)
			throw new ProcessMockException('File must be opened by this driver to be locked', $this->path);
		
		if ($this->isLocked)
			throw new ProcessMockException('File already locked', $this->path);
		
		$isLocked = flock($this->resource, LOCK_EX | LOCK_NB);
		
		if (!$isLocked)
		{
			throw new ProcessMockException(
				'Failed to lock file. Make sure it\'s not locked by another process',
				$this->path);
		}
		
		$this->isLocked = true;
	}
	
	public function release(): void
	{
		if (!$this->isLocked)
			return;
		
		if (!$this->resource)
			throw new ProcessMockException('File must be opened by this driver to be locked/unlocked', $this->path);
		
		$this->isLocked = false;
		flock($this->resource, LOCK_UN);
	}
	
	public function append(string $text, bool $newLine = true): void
	{
		if (!$this->resource)
			throw new ProcessMockException('File must be opened by this driver to write to it', $this->path);
		
		if (!$this->isLocked)
			throw new ProcessMockException('Can write only to file locked by this driver', $this->path);
		
		if ($newLine)
		{
			$text = $text . PHP_EOL;
		}
		
		if (fwrite($this->resource, $text) === false)
		{
			$data = base64_encode($text);
			throw new ProcessMockException("Error while writing to file data $data (base64)", $this->path);
		}
		
		fflush($this->resource);
	}
}