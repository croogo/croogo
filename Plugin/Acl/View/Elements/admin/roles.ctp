<?php
if (isset($roles[$this->data['User']['role_id']])) {
	$validRoles = array_diff_key($roles, array($this->data['User']['role_id'] => null));
} else {
	$validRoles = $roles;
}

echo $this->Form->input('Role', array('values' => $validRoles, 'multiple' => 'checkbox'));
