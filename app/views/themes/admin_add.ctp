<div class="themes">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php echo $form->create('Theme', array('type' => 'file'));?>
        <fieldset>
        <?php
            echo $form->input('Theme.file', array('label' => __('Upload', true), 'type' => 'file',));
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>