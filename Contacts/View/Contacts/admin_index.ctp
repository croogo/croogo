<?php
$this->Html->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Contacts'), '/' . $this->request->url);

$this->extend('/Common/admin_index');
?>
