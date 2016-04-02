<?php
$this->extend('Croogo/Core./Common/admin_index');
$this->Html->addCrumb(__d('croogo', 'Blocks'), ['controller' => 'blocks', 'action' => 'index'])
    ->addCrumb(__d('croogo', 'Regions'));
?>
