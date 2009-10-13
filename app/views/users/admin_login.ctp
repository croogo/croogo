<div class="users form">
    <?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));?>
        <fieldset>
        <?php
            echo $form->input('username');
            echo $form->input('password');
        ?>
        </fieldset>
    <?php echo $form->end('Log In');?>
</div>