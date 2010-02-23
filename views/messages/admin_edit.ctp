<div class="messages form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php echo $form->create('Message');?>
        <fieldset>
        <?php
            echo $form->input('id');
            echo $form->input('name');
            echo $form->input('email');
            echo $form->input('title');
            echo $form->input('body');
            echo $form->input('phone');
            echo $form->input('address');
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>