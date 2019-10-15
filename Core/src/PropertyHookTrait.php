<?php

namespace Croogo\Core;

use Cake\Utility\Hash;

trait PropertyHookTrait
{

    public function getProperty($property)
    {
        $relTypes = ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany'];
        if (in_array($property, $relTypes)) {
            $associations = $this->associations();

            return $associations ? $associations->has($property) : false;
        }

        if (!isset($this->{$property})) {
            return false;
        }

        return $this->{$property};
    }

    public function setProperty($property, $value, $merge = false)
    {
        $relTypes = ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany'];
        if (in_array($property, $relTypes)) {
            $associations = $this->associations();

            return $associations ? $associations->addAssociations($property, $value) : false;
        }

        if ($merge && $this->{$property}) {
            $value = Hash::merge($this->{$property}, $value);
        }
        $this->{$property} = $value;
    }
}
