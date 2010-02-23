<div class="users form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'reset', $username, $key)));?>
        <fieldset>
        <?php
            echo $form->input('password', array('label' => __('New password', true)));
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>