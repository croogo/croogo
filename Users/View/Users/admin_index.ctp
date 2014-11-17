<?php

$this->extend('/Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Users'), '/' . $this->request->url);
?>
