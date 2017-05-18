<?php

$entity = $this->Form->context()->entity();
if ($entity->role_id) {
    $validRoles = array_diff_key($roles, array($entity->role_id => null));
} else {
    $validRoles = $roles;
}

$selected = $entity->roles ?
    \Cake\Utility\Hash::extract($entity->roles, '{n}.id') :
    [];
echo $this->Form->input('roles._ids', [
    'value' => $selected,
    'class' => 'c-select',
    'options' => $validRoles,
    'multiple' => true,
]);
