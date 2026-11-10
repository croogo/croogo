<?php

use Cake\Utility\Hash;

$entity = $this->Form->context()->entity();

// Controllers pass `$roles` as a find('list') query as often as an array;
// array_diff_key() accepts only arrays.
$roles = $roles ?? [];
if ($roles instanceof Traversable) {
    $roles = iterator_to_array($roles);
}

if ($entity && $entity->role_id) {
    $validRoles = array_diff_key($roles, [$entity->role_id => null]);
} else {
    $validRoles = $roles;
}

$selected = $entity && $entity->roles ?
    Hash::extract($entity->roles, '{n}.id') :
    [];
echo $this->Form->input('roles._ids', [
    'value' => $selected,
    'class' => 'c-select',
    'options' => $validRoles,
    'multiple' => true,
]);
