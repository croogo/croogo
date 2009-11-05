<div class="users form">
    <h2><?php __('Add User'); ?></h2>
    <?php echo $form->create('User');?>
    <fieldset>
    <?php
        echo $form->input('role_id');
        echo $form->input('username');
        echo $form->input('password');
        echo $form->input('name');
        echo $form->input('email');
        echo $form->input('website');
        echo $form->input('status');
    ?>
    </fieldset>
    <?php echo $form->end('Submit');?>
</div>