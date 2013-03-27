<?php

$rolePermissions = empty($rolePermissions) ? array() : $rolePermissions;
$out = $this->Form->label('RolePermission', __d('croogo', 'Edit Permissions'));
foreach ($rolePermissions as $role) {
	if ($role['Role']['id'] == 1) {
		continue;
	}
	$field = 'RolePermission.' . $role['Role']['id'];
	$input = $this->Form->checkbox($field, array(
		'checked' => $role['Role']['allowed'] ? 'checked' : null
	));
	$input .= $this->Form->label($field, $role['Role']['title']);
	$out .= $this->Html->div('checkbox', $input);
}
echo $this->Html->div('input select', $out);
