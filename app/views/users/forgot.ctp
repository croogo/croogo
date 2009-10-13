<div class="users form">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'forgot')));?>
        <fieldset>
        <?php
            echo $form->input('username');
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>