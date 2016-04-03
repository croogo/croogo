<?php
$this->extend('Croogo/Core./Common/admin_index');

$this->Html
    ->addCrumb(__d('croogo', 'Users'), ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->addCrumb(__d('croogo', 'Roles'));
