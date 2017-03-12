<?php
$this->extend('Croogo/Core./Common/admin_index');

$this->Html
    ->add(__d('croogo', 'Users'), ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->add(__d('croogo', 'Roles'), $this->request->url);
