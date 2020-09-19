<?php
$this->extend('Croogo/Core./Common/admin_index');
$this->Breadcrumbs
    ->add('Example', ['controller' => 'Example', 'action' => 'index']);
?>
<?php $this->start('actions') ?>
<?php
    echo $this->Croogo->adminAction(
        'New Tab',
        ['action' => 'add']
    );
    echo $this->Croogo->adminAction(
        'Chooser Example',
        ['action' => 'chooser']
    );
    ?>
<?php $this->end() ?>

<p><?= 'content here' ?></p>
