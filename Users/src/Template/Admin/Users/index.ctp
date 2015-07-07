<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Users'), '/' . $this->request->url);
?>
