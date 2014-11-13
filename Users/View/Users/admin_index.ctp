<?php

$this->extend('/Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->icon('home')))
	->addCrumb(__d('croogo', 'Users'), '/' . $this->request->url);
?>
