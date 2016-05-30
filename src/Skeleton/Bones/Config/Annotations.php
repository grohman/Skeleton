<?php
namespace Skeleton\Bones\Config;


use Objection\LiteObject;
use Objection\LiteSetup;


class Annotations extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Skeleton'		=> LiteSetup::createString('skeleton'),
			'Bone'			=> LiteSetup::createString('bone'),
			'UniqueBone'	=> LiteSetup::createString('unique'),
			'InstanceBone'	=> LiteSetup::createString('instance'),
			'StaticBone'	=> LiteSetup::createString('static')
		];
	}
}