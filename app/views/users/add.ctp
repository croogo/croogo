<div class="users form">
    <h2><?php echo $title_for_layout; ?></h2>
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