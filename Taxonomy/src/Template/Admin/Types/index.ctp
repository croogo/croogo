<?php
$this->Html->addCrumb(__d('croogo', 'Content'),
        ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'])
    ->addCrumb(__d('croogo', 'Types'), '/' . $this->request->url);

$this->extend('Croogo/Core./Common/admin_index');
