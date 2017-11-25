<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Settings'), ['plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'index']);
$this->Breadcrumbs->add(__d('croogo', 'Meta'), $this->request->getUri()->getPath());
