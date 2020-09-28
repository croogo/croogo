<?php
declare(strict_types=1);

namespace Croogo\Core;

use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\Utility\Hash;
use UnexpectedValueException;

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
            $alias = key($value);
            $options = $value[$alias];

            switch ($property) {
                case 'hasOne':
                    $association = new HasOne($alias, $options);
                break;
                case 'hasMany':
                    $association = new HasMany($alias, $options);
                break;
                case 'belongsTo':
                    $association = new BelongsTo($alias, $options);
                break;
                case 'belongsToMany':
                    $association = new BelongsToMany($alias, $options);
                break;
            }

            return $associations ? $associations->add($property, $association) : null;
        }

        if ($merge && $this->{$property}) {
            $value = Hash::merge($this->{$property}, $value);
        }
        $this->{$property} = $value;
    }
}
