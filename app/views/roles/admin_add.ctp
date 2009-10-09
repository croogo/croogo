<div class="roles form">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php echo $form->create('Role');?>
        <fieldset>
        <?php
            echo $form->input('title');
            echo $form->input('alias');
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>