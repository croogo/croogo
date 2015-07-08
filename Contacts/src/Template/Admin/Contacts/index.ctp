<?php
$this->extend('Croogo/Core./Common/admin_index');
$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Contacts'), '/' . $this->request->url);

?>
