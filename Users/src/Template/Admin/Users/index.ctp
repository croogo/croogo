<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Users'), $this->request->getUri()->getPath());
