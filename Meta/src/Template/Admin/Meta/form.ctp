<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Settings'), ['plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'index']);
$this->Breadcrumbs->add(__d('croogo', 'Meta'), ['action' => 'index']);

if ($this->getRequest()->params['action'] == 'edit') {
    $this->Breadcrumbs->add(h($$viewVar->key), $this->getRequest()->getRequestTarget());

    $this->assign('title', __d('croogo', 'Edit Meta'));
}

if ($this->getRequest()->params['action'] == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());

    $this->assign('title', __d('croogo', 'Add Meta'));
}
