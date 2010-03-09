<div class="users form">
    <h2><?php __('Reset password'); ?>: <?php echo $this->data['User']['username']; ?></h2>
    <?php echo $form->create('User', array('url' => array('action' => 'reset_password')));?>
        <fieldset>
        <?php
            echo $form->input('id');
            echo $form->input('username', array('type' => 'hidden'));
            echo $form->input('current_password', array('label' => __('Current Password', true), 'type' => 'password', 'value' => ''));
            echo $form->input('password', array('label' => __('New Password', true), 'value' => ''));
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>