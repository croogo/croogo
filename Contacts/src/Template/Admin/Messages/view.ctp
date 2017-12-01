<?php

$this->extend('Croogo/Core./Common/admin_view');

$this->Breadcrumbs
    ->add(__d('croogo', 'Messages'), ['action' => 'index']);

    $this->Breadcrumbs->add($message->title, $this->request->getRequestTarget());

$this->append('action-buttons');
    echo $this->Croogo->adminAction(__d('croogo', 'List Messages'), ['action' => 'index']);
$this->end();

$this->append('main');
?>
<div class="messages view large-9 medium-8 columns">
    <table class="table vertical-table">
        <tr>
            <th scope="row"><?= __d('croogo', 'Contact') ?></th>
            <td><?= $message->has('contact') ? $this->Html->link($message->contact->name, ['controller' => 'Contacts', 'action' => 'view', $message->contact->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Name') ?></th>
            <td><?= h($message->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Email') ?></th>
            <td><?= h($message->email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Title') ?></th>
            <td><?= h($message->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Website') ?></th>
            <td><?= h($message->website) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Phone') ?></th>
            <td><?= h($message->phone) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Message Type') ?></th>
            <td><?= h($message->message_type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Trackable Updater') ?></th>
            <td><?= $message->has('trackable_updater') ? $this->Html->link($message->trackable_updater->name, ['controller' => 'Users', 'action' => 'view', $message->trackable_updater->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Trackable Creator') ?></th>
            <td><?= $message->has('trackable_creator') ? $this->Html->link($message->trackable_creator->name, ['controller' => 'Users', 'action' => 'view', $message->trackable_creator->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Id') ?></th>
            <td><?= $this->Number->format($message->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Updated') ?></th>
            <td><?= $this->Time->i18nFormat($message->updated) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Created') ?></th>
            <td><?= $this->Time->i18nFormat($message->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Status') ?></th>
            <td><?= $message->status ? __d('croogo', 'Yes') : __d('croogo', 'No'); ?></td>
        </tr>
    </table>
    <div>
        <label>
            <strong><?= __d('croogo', 'Body') ?></strong>
        </label>
        <?= $this->Text->autoParagraph(h($message->body)); ?>
    </div>
    <div>
        <label>
            <strong><?= __d('croogo', 'Address') ?></strong>
        </label>
        <?= $this->Text->autoParagraph(h($message->address)); ?>
    </div>
</div>
<?php
$this->end();
