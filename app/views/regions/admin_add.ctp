<div class="regions form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('Region');?>
        <fieldset>
        <?php
            echo $form->input('title');
            echo $form->input('alias');
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>