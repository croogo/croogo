<div class="languages form">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php echo $form->create('Language'); ?>
        <fieldset>
        <?php
            echo $form->input('id');
            echo $form->input('title');
            echo $form->input('native');
            echo $form->input('alias');
            echo $form->input('status');
        ?>
        </fieldset>
    <?php echo $form->end('Submit'); ?>
</div>