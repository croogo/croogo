<?php
/**
 * @var \App\View\AppView $this
 */

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Settings'), ['plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'index']);
$this->Breadcrumbs->add(__d('croogo', 'Meta'), $this->getRequest()->getUri()->getPath());
