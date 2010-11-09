<div class="users form">
    <h2><?php __('Edit User'); ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Reset password', true), array('action' => 'reset_password', $this->params['pass']['0'])); ?></li>
        </ul>
    </div>

    <?php echo $this->Form->create('User');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#user-main"><?php __('User'); ?></a></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="user-main">
                <?php
                    echo $this->Form->input('id');
                    echo $this->Form->input('role_id');
                    echo $this->Form->input('username');
                    echo $this->Form->input('name');
                    echo $this->Form->input('email');
                    echo $this->Form->input('website');
                    echo $this->Form->input('status');
                ?>
                </div>
                <?php echo $layout->adminTabs(); ?>
        </fieldset>
    <?php echo $this->Form->end('Submit');?>
</div>