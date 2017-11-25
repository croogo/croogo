<?php

$this->extend('/Common/admin_index');
$this->Breadcrumbs
    ->add('Example', array('controller' => 'Example', 'action' => 'index'))
    ->add('Chooser Example', array('controller' => 'Example', 'action' => 'chooser'));

$this->append('form-start', $this->Form->create(null));

$this->append('main');

echo $this->element('Croogo/Core.admin/modal', [
    'id' => 'link-chooser',
]);

echo $this->Form->input('node_id', array(
    'type' => 'text',
    'data-attr' => 'data-chooser-id',
    'append' => $this->Html->link('Choose Node',
        array(
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'index',
            '?' => array(
                'chooser' => true,
            ),
        ),
        array(
            'class' => 'item-choose',
            'data-chooser-type' => 'Node',
            'data-chooser-target' => '#node-id',
            'data-attr' => 'data-chooser-id',
            'data-target' => '#link-chooser',
            'data-toggle' => 'modal',
            'data-chooser' => true,
        )
    )
));

echo $this->Form->input('node_url', array(
    'type' => 'text',
    'data-attr' => 'rel',
    'append' => $this->Html->link('Choose Node',
        array(
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'index',
            '?' => array(
                'chooser' => true,
            ),
        ),
        array(
            'class' => 'item-choose',
            'data-chooser-type' => 'Node',
            'data-chooser-target' => '#node-url',
            'data-attr' => 'rel',
            'data-target' => '#link-chooser',
            'data-toggle' => 'modal',
            'data-chooser' => true,
        )
    )
));

echo $this->Form->input('block_id', array(
    'type' => 'text',
    'data-attr' => 'data-chooser-id',
    'append' => $this->Html->link('Choose Block Id',
        array(
            'plugin' => 'Croogo/Blocks',
            'controller' => 'Blocks',
            'action' => 'index',
            '?' => array(
                'chooser' => true,
            ),
        ),
        array(
            'class' => 'item-choose',
            'data-chooser-type' => 'Block',
            'data-chooser-target' => '#block-id',
            'data-attr' => 'data-chooser-id',
            'data-target' => '#link-chooser',
            'data-toggle' => 'modal',
            'data-chooser' => true,
        )
    )
));

echo $this->Form->input('block_title', array(
    'type' => 'text',
    'data-attr' => 'data-chooser-title',
    'append' => $this->Html->link('Choose Block Title',
        array(
            'plugin' => 'Croogo/Blocks',
            'controller' => 'Blocks',
            'action' => 'index',
            '?' => array(
                'chooser' => true,
            ),
        ),
        array(
            'class' => 'item-choose',
            'data-chooser-type' => 'Block',
            'data-chooser-target' => '#block-title',
            'data-attr' => 'data-chooser-title',
            'data-target' => '#link-chooser',
            'data-toggle' => 'modal',
            'data-chooser' => true,
        )
    )
));

echo $this->Form->input('user_id', array(
    'type' => 'text',
    'data-attr' => 'data-chooser-id',
    'append' => $this->Html->link('Choose User Id',
        array(
            'plugin' => 'Croogo/Users',
            'controller' => 'Users',
            'action' => 'index',
            '?' => array(
                'chooser' => true,
            ),
        ),
        array(
            'class' => 'item-choose',
            'data-chooser-type' => 'User',
            'data-chooser-target' => '#user-id',
            'data-attr' => 'data-chooser-id',
            'data-target' => '#link-chooser',
            'data-toggle' => 'modal',
            'data-chooser' => true,
        )
    )
));

$this->end();

$script = <<<EOF
$('#node-id, #node-url, #block-id, #block-title, #user-id')
    .on('chooserSelect', function(event, data) {
        var element = $(this)
        element.val($(data).attr(element.data('attr')));
    });
EOF;
$this->append('scriptBottom', $this->Html->scriptBlock($script));
