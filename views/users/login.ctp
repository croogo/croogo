<div class="users form">
    <h2><?php __('Login'); ?></h2>
    <?php echo $this->Form->create($userModel, array('url' => $loginAction));?>
        <fieldset>
        <?php
            echo $this->Form->input($fields['username']);
            echo $this->Form->input($fields['password']);
        ?>
        </fieldset>
    <?php echo $this->Form->end('Submit');?>
</div>