<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Meta'), $this->request->getUri()->getPath());
