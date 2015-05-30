<?php

namespace Croogo\Croogo\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use Croogo\Croogo\Link;

class LinkType extends Type {

	public function toPHP($value, Driver $driver)
	{
		if (stristr($value, 'controller:')) {
			return Link::createFromLinkString($value);
		} else {
			return new Link($value);
		}
	}

	public function marshal($value)
	{
		if (is_null($value)) {
			return null;
		}

		if (strstr($value, 'controller:')) {
			return Link::createFromLinkString($value);
		} else {
			return new Link($value);
		}
	}

	public function toDatabase($value, Driver $driver)
	{
		return (string) $value;
	}

}
