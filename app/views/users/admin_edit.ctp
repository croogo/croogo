<div class="users form">
    <h2><?php __('Edit User'); ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('Reset password', true), array('action' => 'reset_password', $this->params['pass']['0'])); ?></li>
        </ul>
    </div>

    <?php echo $form->create('User');?>
        <fieldset>
        <?php
            echo $form->input('id');
            echo $form->input('role_id');
            echo $form->input('username');
            echo $form->input('name');
            echo $form->input('email');
            echo $form->input('website');
            echo $form->input('status');
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>