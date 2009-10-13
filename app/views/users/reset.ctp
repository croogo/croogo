<div class="users form">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'reset', $username, $key)));?>
        <fieldset>
        <?php
            echo $form->input('password', array('label' => __('New password', true)));
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>