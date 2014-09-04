<?php
if (isset($roles[$this->request->data['User']['role_id']])) {
	$validRoles = array_diff_key($roles, array($this->request->data['User']['role_id'] => null));
} else {
	$validRoles = $roles;
}

echo $this->Form->input('Role', array('values' => $validRoles, 'class' => 'input checkbox', 'multiple' => 'checkbox'));
