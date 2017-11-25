<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs
    ->add(__d('croogo', 'Content'), ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'])
    ->add(__d('croogo', 'Types'), ['plugin' => 'Croogo/Taxonomy', 'controller' => 'Types', 'action' => 'index']);

if ($this->request->params['action'] == 'edit') {
    $this->assign('title', __d('croogo', 'Edit Type'));

    $this->Breadcrumbs->add($type->title, $this->request->getRequestTarget());
}

if ($this->request->params['action'] == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
}

$this->append('form-start', $this->Form->create($type));

$this->start('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Type'), '#type-main');
    echo $this->Croogo->adminTab(__d('croogo', 'Taxonomy'), '#type-taxonomy');
    echo $this->Croogo->adminTab(__d('croogo', 'Comments'), '#type-comments');
    echo $this->Croogo->adminTab(__d('croogo', 'Params'), '#type-params');
$this->end();

$this->start('tab-content');

    echo $this->Html->tabStart('type-main');
        echo $this->Form->input('title', [
            'label' => __d('croogo', 'Title'),
            'data-slug' => '#alias',
        ]);
        echo $this->Form->input('alias', [
            'label' => __d('croogo', 'Alias'),
            'label' => __d('croogo', 'Permalink'),
            'prepend' => str_replace('_placeholder', '', $this->Url->build([
                'prefix' => false,
                'plugin' => 'Croogo/Nodes',
                'controller' => 'Nodes',
                'action' => 'index',
                'type' => '_placeholder',
            ], ['fullbase' => true]))
        ]);
        echo $this->Form->input('description', [
            'label' => __d('croogo', 'Description'),
        ]);
        echo $this->Html->tabEnd();
        echo $this->Html->tabStart('type-taxonomy');
        echo $this->Form->input('vocabularies._ids', [
            'class' => 'c-select',
            'multiple' => 'checkbox'
        ]);
    echo $this->Html->tabEnd();

    echo $this->Html->tabStart('type-comments');
        echo $this->Form->input('comment_status', [
            'type' => 'radio',
            'options' => [
                '0' => __d('croogo', 'Disabled'),
                '1' => __d('croogo', 'Read only'),
                '2' => __d('croogo', 'Read/Write'),
            ],
            'default' => 2,
            'label' => __d('croogo', 'Commenting'),
        ]);
        echo $this->Form->input('comment_approve', [
            'label' => 'Auto approve comments',
            'class' => false,
        ]);
        echo $this->Form->input('comment_spam_protection', [
            'label' => __d('croogo', 'Spam protection (requires Akismet API key)'),
            'class' => false,
        ]);
        echo $this->Form->input('comment_captcha', [
            'label' => __d('croogo', 'Use captcha? (requires Recaptcha API key)'),
            'class' => false,
        ]);
        echo $this->Html->link(__d('croogo', 'You can manage your API keys here.'), [
            'plugin' => 'Croogo/Settings',
            'controller' => 'Settings',
            'action' => 'prefix',
            'Service',
        ]);
    echo $this->Html->tabEnd();

    echo $this->Html->tabStart('type-params');
        echo $this->Form->input('params', [
            'type' => 'stringlist',
            'label' => __d('croogo', 'Params'),
            'default' => 'routes=true',
        ]);
    echo $this->Html->tabEnd();

$this->end();

$this->start('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
    echo $this->element('Croogo/Core.admin/buttons', ['type' => 'type']);
    echo $this->Form->input('format_show_author', [
        'label' => __d('croogo', 'Show author\'s name'),
        'class' => false,
    ]);
    echo $this->Form->input('format_show_date', [
        'label' => __d('croogo', 'Show date'),
        'class' => false,
    ]);
    echo $this->Form->input('format_use_wysiwyg', [
        'label' => __d('croogo', 'Use rich editor'),
        'class' => false,
        'default' => true
    ]);
    echo $this->Html->endBox();
$this->end();
