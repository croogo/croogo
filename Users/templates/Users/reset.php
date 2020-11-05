<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Users\Model\Entity\User $user
 */
?>
<div class="users form">
    <h2><?= $this->fetch('title'); ?></h2>
    <?= $this->Form->create($user); ?>
    <fieldset>
        <?= $this->Form->control('password', ['label' => __d('croogo', 'New password'), 'value' => '']); ?>
        <?= $this->Form->control('verify_password', ['type' => 'password', 'label' => __d('croogo', 'Verify Password')]); ?>
    </fieldset>
    <?= $this->Form->submit(__d('croogo', 'Reset')); ?>
    <?= $this->Form->end(); ?>
</div>
