<?php
/**
 * @var \App\View\AppView $this
 */
$this->extend('Croogo/Core./Common/admin_index');
$this->Breadcrumbs->add(__d('croogo', 'Blocks'), ['controller' => 'Blocks', 'action' => 'index'])
    ->add(__d('croogo', 'Regions'), $this->getRequest()->getUri()->getPath());
?>
