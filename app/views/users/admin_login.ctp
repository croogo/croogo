<div class="users form">
    <?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));?>
        <fieldset>
        <?php
            echo $form->input('username');
            echo $form->input('password');
        ?>
        </fieldset>
    <?php
        echo $html->link(__('Forgot password?', true), array(
            'admin' => false,
            'controller' => 'users',
            'action' => 'forgot',
        ), array(
            'class' => 'forgot',
        ));
        echo $form->end(__('Log In', true));
    ?>
</div>