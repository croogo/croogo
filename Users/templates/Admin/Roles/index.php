<?php
/**
 * @var \App\View\AppView $this
 */
$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->add(__d('croogo', 'Roles'), $this->getRequest()->getUri()->getPath());
