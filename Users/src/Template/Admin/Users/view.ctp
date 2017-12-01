<?php

$this->extend('Croogo/Core./Common/admin_view');

$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), ['action' => 'index'])
    ->add($user->name, $this->request->getRequestTarget());

$this->append('action-buttons');
    echo $this->Croogo->adminAction(__d('croogo', 'Edit User'), ['action' => 'edit', $user->id]);
$this->end();

$this->append('main');
?>
<div class="users view large-9 medium-8 columns">
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __d('croogo', 'Role') ?></th>
            <td><?= $user->has('role') ? $this->Html->link($user->role->title, ['controller' => 'Roles', 'action' => 'view', $user->role->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Username') ?></th>
            <td><?= h($user->username) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Name') ?></th>
            <td><?= h($user->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Website') ?></th>
            <td><?= h($user->website) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Timezone') ?></th>
            <td><?= h($user->timezone) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Updated By') ?></th>
            <td><?= $this->Number->format($user->updated_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Created By') ?></th>
            <td><?= $this->Number->format($user->created_by) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Updated') ?></th>
            <td><?= $this->Time->i18nFormat($user->updated) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Created') ?></th>
            <td><?= $this->Time->i18nFormat($user->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __d('croogo', 'Status') ?></th>
            <td><?= $user->status ? __d('croogo', 'Yes') : __d('croogo', 'No'); ?></td>
        </tr>
    </table>
    <div>
        <label>
            <strong><?= __d('croogo', 'Bio') ?></strong>
        </label>
        <?= $this->Text->autoParagraph(h($user->bio)); ?>
    </div>
</div>
<?php
$this->end();
