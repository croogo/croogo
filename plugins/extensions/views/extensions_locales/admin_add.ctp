<div class="extensions-locales">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php
        echo $this->Form->create('Locale', array(
            'url' => array(
                'plugin' => 'extensions',
                'controller' => 'extensions_locales',
                'action' => 'add',
            ),
            'type' => 'file',
        ));
    ?>
    <fieldset>
    <?php
        echo $this->Form->input('Locale.file', array('label' => __('Upload', true), 'type' => 'file',));
    ?>
    </fieldset>

    <div class="buttons">
    <?php
        echo $this->Form->end(__('Upload', true));
        echo $this->Html->link(__('Cancel', true), array(
            'action' => 'index',
        ), array(
            'class' => 'cancel',
        ));
    ?>
    </div>
</div>