<?php

namespace Croogo\Core\Database\Type;

use Cake\Database\DriverInterface;
use Cake\Database\TypeInterface;
use Cake\Utility\Text;
use Croogo\Core\Link;
use PDO;

class LinkType implements TypeInterface
{
    public function getBaseType(): ?string
    {
        return ParamsType::class;
    }

    public function getName(): ?string {
        return 'Params';
    }

    public function newId()
    {
        return Text::uuid();
    }

    public function toPHP($value, DriverInterface $driver)
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

    public function toDatabase($value, DriverInterface $driver)
    {
        return (string)$value;
    }

    public function toStatement($value, DriverInterface $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }

        return PDO::PARAM_STR;
    }}
