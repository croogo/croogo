<?php

$rolePermissions = empty($rolePermissions) ? array() : $rolePermissions;
$out = $this->Form->label('RolePermission', __d('croogo', 'Edit Permissions'));
foreach ($rolePermissions as $role) {
    if ($role['Role']['id'] == 1) {
        continue;
    }
    $field = 'RolePermission.' . $role['Role']['id'];
    $input = $this->Form->input($field, [
        'type' => 'checkbox',
        'checked' => $role['Role']['allowed'] ? true : false,
        'label' => $role['Role']['title'],
    ]);
    $out .= $input;
}
echo $out;
