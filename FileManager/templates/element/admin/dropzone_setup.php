<?php
/**
 * @var \App\View\AppView $this
 * @var string $type
 */

echo $this->Html->tag(
    'div',
    $this->Html->tag('p', __d('croogo', 'Drop files here to upload')),
    [
        'id' => 'dropzone-target',
        'data-base-url' => $this->Url->build('/', true),
        'data-csrf-token' => $this->getRequest()->getParam('_csrfToken'),
        'data-url' => $this->Url->build([
            'action' => 'add',
            'prefix' => 'Admin',
            'controller' => 'Attachments',
            'plugin' => 'Croogo/FileManager'
        ], true),
    ]
);
echo $this->Html->tag(
    'script',
    $this->element('Croogo/FileManager.admin/dropzone_' . $type . '_preview'),
    ['id' => 'dropzone-preview', 'type' => 'text/html']
);
$this->Form->create(null, [
    'url' => [
        'action' => 'add',
        'prefix' => 'Admin',
        'controller' => 'Attachments',
        'plugin' => 'Croogo/FileManager'
    ],
]);
$this->Form->unlockField('file');
echo $this->Html->tag('div', $this->Form->secure([]), ['id' => 'tokens']);
$this->Form->end();
