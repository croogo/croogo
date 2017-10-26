<?php

$this->extend('Croogo/Core./Common/admin_view');

$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), ['action' => 'index'])
    ->add($user->name, $this->request->getRequestTarget());

$this->append('action-buttons');
    echo $this->Croogo->adminAction(__('Edit User'), ['action' => 'edit', $user->id]);
$this->end();

$this->append('main');
?>
<div class="users view large-9 medium-8 columns">
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Role') ?></th>
            <td><?= $user->has('role') ? $this->Html->link($user->role->title, ['controller' => 'Roles', 'action' => 'view', $user->role->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Username') ?></th>
            <td><?= h($user->username) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($user->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Website') ?></th>
            <td><?= h($user->website) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Timezone') ?></th>
            <td><?= h($user->timezone) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Updated By') ?></th>
            <td><?= $this->Number->format($user->updated_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created By') ?></th>
            <td><?= $this->Number->format($user->created_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Updated') ?></th>
            <td><?= $this->Time->i18nFormat($user->updated) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= $this->Time->i18nFormat($user->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= $user->status ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div>
        <label>
            <strong><?= __('Bio') ?></strong>
        </label>
        <?= $this->Text->autoParagraph(h($user->bio)); ?>
    </div>
</div>
<?php
$this->end();
