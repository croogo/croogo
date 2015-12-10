<?php

namespace Croogo\Core;

trait PropertyHookTrait
{

    public function getProperty($property)
    {
        if (!isset($this->{$property})) {
            return false;
        }
        return $this->{$property};
    }

    public function setProperty($property, $value, $merge = false)
    {
        if ($merge && $this->{$property}) {
            $value = Hash::merge($this->{$property}, $value);
        }
        $this->{$property} = $value;
    }
}
