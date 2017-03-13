<?php
echo $this->Form->create($user);
?>
    <div class="install">
        <h2><?php echo __d('croogo', 'Step 3: Create Admin User'); ?></h2>
        <?php
        echo $this->Form->input('username', [
            'placeholder' => __d('croogo', 'Username'),
            'prepend' => $this->Html->icon('user'),
            'label' => false,
        ]);
        echo $this->Form->input('password', [
            'placeholder' => __d('croogo', 'New Password'),
            'value' => '',
            'prepend' => $this->Html->icon('key'),
            'label' => false,
        ]);
        echo $this->Form->input('verify_password', [
            'placeholder' => __d('croogo', 'Verify Password'),
            'type' => 'password',
            'value' => '',
            'prepend' => $this->Html->icon('key'),
            'label' => false,
        ]);
        ?>
    </div>
    <div class="form-actions">
        <?php echo $this->Form->submit(__d('croogo', 'Create admin user'), ['class' => 'success', 'div' => false]); ?>
    </div>
<?php
echo $this->Form->end();
?>
