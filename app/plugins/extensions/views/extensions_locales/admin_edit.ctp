<div class="extensions-locales">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php
        echo $form->create('Locale', array(
            'url' => array(
                'plugin' => 'extensions',
                'controller' => 'extensions_locales',
                'action' => 'edit',
                $locale
            ),
        ));
    ?>
    <fieldset>
    <?php
        echo $form->input('Locale.content', array(
            'label' => __('Content', true),
            'value' => $content,
            'type' => 'textarea',
            'class' => 'file-content',
        ));
    ?>
    </fieldset>
    <?php echo $form->end('Submit');?>
</div>