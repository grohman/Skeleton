<?php
namespace Skeleton\Bones;


use Objection\Enum\AccessRestriction;
use Objection\LiteObject;
use Objection\LiteSetup;

use Skeleton\Bones\Config\Annotations;
use Skeleton\Type;


class Config extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Annotations'	=> LiteSetup::createInstanceOf(new Annotations(), AccessRestriction::NO_SET),
			'DefaultType'	=> Type::Instance
		];
	}
}