<?php
namespace Skeleton\Base;


use Skeleton\Type;


interface IMap
{
	public function loader(): ILoader;
	public function setLoader(ILoader $loader): void;
	public function enableKnot(ISkeletonSource $skeletonSource): void;
	public function set(string $key, $value, int $flags = Type::Instance): void;
	public function forceSet(string $key, $value, int $flags = Type::Instance): void;
	public function has(string $key): bool;
	
	/**
	 * @param string $key
	 * @param IContextReference|null $context
	 * @return mixed
	 */
	public function get(string $key, ?IContextReference $context = null);
}