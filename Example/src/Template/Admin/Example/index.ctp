<?php
$this->extend('Croogo/Core./Common/admin_index');
$this->Breadcrumbs
    ->add('Example', array('controller' => 'example', 'action' => 'index'));
?>
<?php $this->start('actions') ?>
<?php
    echo $this->Croogo->adminAction(
        'New Tab',
        array('action' => 'add')
    );
    echo $this->Croogo->adminAction(
        'Chooser Example',
        array('action' => 'chooser')
    );
?>
<?php $this->end() ?>

<p><?= 'content here' ?></p>
