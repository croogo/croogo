<div class="users form">
    <h2><?= __d('croogo', 'Login') ?></h2>
    <?= $this->Form->create(null, ['url' => ['action' => 'login']]);?>
        <fieldset>
        <?php
            echo $this->Form->input('username');
            echo $this->Form->input('password');
        ?>
        </fieldset>
    <?= $this->Form->submit('Submit') ?>
    <?= $this->Form->end();?>
</div>
