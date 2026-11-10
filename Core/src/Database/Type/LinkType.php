<?php

namespace Croogo\Core\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type\BaseType;
use Croogo\Core\Link;

class LinkType extends BaseType
{

    public function toPHP(mixed $value, Driver $driver): mixed
    {
        if (stristr($value, 'controller:')) {
            return Link::createFromLinkString($value);
        } else {
            return new Link($value);
        }
    }

    public function marshal(mixed $value): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value)) {
            $value = array_map(function ($val) {
                return str_replace('/', '%2f', $val);
            }, $value);
            if (count($value) === 1) {
                $value = $value[0];
            } else {
                $value = implode('/', $value);
            }
        }

        if (strstr($value, 'controller:')) {
            return Link::createFromLinkString($value);
        } else {
            return new Link($value);
        }
    }

    public function toDatabase(mixed $value, Driver $driver): mixed
    {
        return (string)$value;
    }
}
