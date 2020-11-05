<?php
/**
 * @var \App\View\AppView $this
 */

echo $this->Form->create(null, [
    'url' => [
        'prefix' => false,
        'plugin' => 'Croogo/Nodes',
        'controller' => 'Nodes',
        'action' => 'search'
    ],
]);

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
            'secure' => false,
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
