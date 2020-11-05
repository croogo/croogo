<?php
/**
 * @var \App\View\AppView $this
 */

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Users'), $this->getRequest()->getUri()->getPath());
