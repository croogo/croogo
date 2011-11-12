<div class="users form">
    <?php echo $this->Form->create($userModel, array('url' => $loginAction));?>
        <fieldset>
        <?php
            echo $this->Form->input($fields['username']);
            echo $this->Form->input($fields['password']);
        ?>
        </fieldset>
    <?php
        echo $this->Html->link(__('Forgot password?', true), array(
            'admin' => false,
            'controller' => 'users',
            'action' => 'forgot',
        ), array(
            'class' => 'forgot',
        ));
        echo $this->Form->end(__('Log In', true));
    ?>
</div>