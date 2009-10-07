<div class="regions form">
    <h2><?php echo $this->pageTitle; ?></h2>

    <?php echo $form->create('Region');?>
        <fieldset>
        <?php
            echo $form->input('id');
            echo $form->input('title');
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>