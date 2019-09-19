<?php

$this->assign('title', __d('croogo', 'Forgot Password'));

?>
<div class="users form">
    <h2><?= $this->fetch('title') ?></h2>
    <?= $this->Form->create('User', ['url' => ['controller' => 'Users', 'action' => 'forgot']]);?>
        <fieldset>
        <?= $this->Form->input('username', [
            'label' => __d('croogo', 'Username'),
            'required' => true,
        ]) ?>
        </fieldset>
        <?= $this->Form->submit(__d('croogo', 'Submit')) ?>
    <?= $this->Form->end() ?>
</div>
