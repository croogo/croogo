<?php
/**
 * @var \Croogo\Core\View\CroogoView $this
 */

use Croogo\Core\Status;

echo $this->Form->control('status', [
    'label' => __d('croogo', 'Status'),
    'class' => 'c-select',
    'default' => Status::UNPUBLISHED,
    'options' => $this->Croogo->statuses(),
]);

echo $this->Html->div('input-daterange', $this->Form->control('publish_start', [
        'label' => __d('croogo', 'Publish on'),
        'data-format' => 'Y-m-d H:i:s',
        'empty' => true,
    ]) . $this->Form->control('publish_end', [
        'label' => __d('croogo', 'Un-publish on'),
        'data-format' => 'Y-m-d H:i:s',
        'empty' => true,
    ]));
