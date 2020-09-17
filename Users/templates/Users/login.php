<div class="users form">
    <h2><?= __d('croogo', 'Login') ?></h2>
    <?= $this->Form->create(null, ['url' => ['action' => 'login']]);?>
        <fieldset>
        <?php
            echo $this->Form->control('username');
            echo $this->Form->control('password');
        ?>
        </fieldset>
    <?= $this->Form->submit('Submit') ?>
    <?= $this->Form->end();?>
</div>
