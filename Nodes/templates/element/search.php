<?php

echo $this->Form->create(null, [
    'url' => [
        'prefix' => false,
        'plugin' => 'Croogo/Nodes',
        'controller' => 'Nodes',
        'action' => 'search'
    ],
]);

$this->Form->unlockField('q');

?>
<div class="input-group">

    <?=
        $this->Form->control('q', [
            'label' => false,
            'default' => $this->getRequest()->getQuery('q'),
            'templates' => [
                'inputContainer' => '{{content}}',
            ],
            'required' => true,
        ]);
?>

    <span class="input-group-btn">
        <?=
            $this->Form->button(__d('croogo', 'Search'), [
                'class' => 'btn btn-secondary',
            ])
?>
    </span>
</div>
<?= $this->Form->end(); ?>
