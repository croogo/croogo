<div class="users form">
    <h2><?php echo $this->fetch('title'); ?></h2>
    <?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'forgot')));?>
        <fieldset>
        <?php
            echo $this->Form->input('username', array('label' => __d('croogo', 'Username')));
        ?>
        </fieldset>
        <?php echo $this->Form->submit(__d('croogo', 'Submit')); ?>
    <?php echo $this->Form->end();?>
</div>
