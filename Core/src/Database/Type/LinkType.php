<?php

namespace Croogo\Core\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use Croogo\Core\Link;

class LinkType extends Type
{

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
        return (string)implode('/', (array)$value);
    }
}
