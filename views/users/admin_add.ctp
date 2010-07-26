<div class="users form">
    <h2><?php __('Add User'); ?></h2>
    <?php echo $form->create('User');?>
    <fieldset>
        <div class="tabs">
            <ul>
                <li><a href="#user-main"><?php __('User'); ?></a></li>
                <?php echo $layout->adminTabs(); ?>
            </ul>

            <div id="user-main">
            <?php
                echo $form->input('role_id');
                echo $form->input('username');
                echo $form->input('password');
                echo $form->input('name');
                echo $form->input('email');
                echo $form->input('website');
                echo $form->input('status');
            ?>
            </div>
            <?php echo $layout->adminTabs(); ?>
        </div>
    </fieldset>
    <?php echo $form->end('Submit');?>
</div>