<?php
$this->extend('Croogo/Core./Common/admin_index');
$this->Breadcrumbs->add(__d('croogo', 'Contacts'), $this->getRequest()->getUri()->getPath());
