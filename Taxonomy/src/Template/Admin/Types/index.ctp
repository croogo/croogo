<?php
$this->Breadcrumbs->add(
    __d('croogo', 'Content'),
    ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index']
)
    ->add(__d('croogo', 'Types'), $this->getRequest()->getRequestTarget());

$this->extend('Croogo/Core./Common/admin_index');
