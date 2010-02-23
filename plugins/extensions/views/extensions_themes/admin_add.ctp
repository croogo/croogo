<div class="extensions-themes">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php
        echo $form->create('Theme', array(
            'url' => array(
                'plugin' => 'extensions',
                'controller' => 'extensions_themes',
                'action' => 'add',
            ),
            'type' => 'file',
        ));
    ?>
    <fieldset>
    <?php
        echo $form->input('Theme.file', array('label' => __('Upload', true), 'type' => 'file',));
    ?>
    </fieldset>
    <?php echo $form->end('Submit');?>
</div>