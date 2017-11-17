<div class="users form">
    <h2><?= $this->fetch('title') ?></h2>
    <?= $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'forgot')));?>
        <fieldset>
        <?php
            echo $this->Form->input('username', array('label' => __d('croogo', 'Username')));
        ?>
        </fieldset>
        <?= $this->Form->submit(__d('croogo', 'Submit')) ?>
    <?= $this->Form->end();?>
</div>
