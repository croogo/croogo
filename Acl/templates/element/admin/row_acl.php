<?php

$rolePermissions = empty($rolePermissions) ? array() : $rolePermissions;
$out = $this->Form->label('rolePermissions', __d('croogo', 'Edit Permissions'));
foreach ($rolePermissions as $role) {
    if ($role['id'] == 1) {
        continue;
    }
    $field = 'rolePermissions.' . $role['id'];
    $input = $this->Form->input($field, [
        'type' => 'checkbox',
        'checked' => $role['allowed'] ? true : false,
        'label' => $role['title'],
    ]);
    $out .= $input;
}
echo $out;
