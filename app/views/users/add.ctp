<div class="users form">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php echo $form->create('User');?>
        <fieldset>
        <?php
            echo $form->input('username');
            echo $form->input('password', array('value' => ''));
            echo $form->input('name');
            echo $form->input('email');
            echo $form->input('website');
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>