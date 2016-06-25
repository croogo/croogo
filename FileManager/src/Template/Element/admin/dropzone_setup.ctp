<?php
/**
 * @var \uAfrica\View\AppView $this
 */

echo $this->Html->tag('span', $this->Url->build(['action' => 'add', 'prefix' => 'admin']),
    ['id' => 'dropzone-url', 'class' => 'hidden']);
echo $this->Html->tag('span', $this->Url->build('/', true), ['id' => 'base-url', 'class' => 'hidden']);
echo $this->Html->tag('div', $this->Html->tag('p', __d('croogo', 'Drop files here to upload')),
    ['id' => 'dropzone-target']);
echo $this->Html->tag('script',
    $this->element('Croogo/FileManager.admin/dropzone_' . $type . '_preview'),
    ['id' => 'dropzone-preview', 'type' => 'text/html']
);
$this->Form->create(null, ['url' => ['action' => 'add', 'prefix' => 'admin']]);
$this->Form->unlockField('file');
echo $this->Html->tag('div', $this->Form->secure([]), ['id' => 'tokens']);
$this->Form->end();
