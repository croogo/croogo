<?php

$this->assign('title', __d('croogo', 'Registration'));

?>
<div class="users form">
    <h2><?= $this->fetch('title') ?></h2>
    <?= $this->Form->create('User') ?>
        <fieldset>
        <?php
            echo $this->Form->control('username');
            echo $this->Form->control('password', ['value' => '']);
            echo $this->Form->control('verify_password', ['type' => 'password', 'value' => '']);
            echo $this->Form->control('name');
            echo $this->Form->control('email');
            echo $this->Form->control('website');

            echo $this->Form->submit(__d('croogo', 'Register'));
        ?>
        </fieldset>
    <?= $this->Form->end() ?>
</div>