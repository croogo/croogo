<?php

$this->extend('/Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Extensions'),
        ['plugin' => 'Croogo/Extensions', 'controller' => 'Plugins', 'action' => 'index'])
    ->add(__d('croogo', 'Themes'),
        ['plugin' => 'Croogo/Extensions', 'controller' => 'Themes', 'action' => 'index'])
    ->add(__d('croogo', 'Upload'), $this->getRequest()->getRequestTarget());

$this->append('form-start', $this->Form->create(null, [
    'url' => [
        'plugin' => 'Croogo/Extensions',
        'controller' => 'Themes',
        'action' => 'add',
    ],
    'type' => 'file',
]));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#themes-upload');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('themes-upload') . $this->Form->input('Theme.file', [
        'type' => 'file',
        'class' => 'c-file',
        'required' => true,
    ]);
echo $this->Html->tabEnd();
$this->end();

$this->append('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
?>
<div class="clearfix">
    <div class="card-buttons d-flex justify-content-center">
    <?php
        echo $this->Form->button(__d('croogo', 'Upload'), [
            'class' => 'btn-outline-primary',
        ]);
        echo $this->Html->link(__d('croogo', 'Cancel'), [
            'action' => 'index',
        ], [
            'button' => 'outline-danger',
        ]);
    ?>
    </div>
</div>
<?php
echo $this->Html->endBox();
$this->end();
