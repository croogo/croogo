<div class="extensions-locales">
<<<<<<< HEAD
    <h2><?php echo $title_for_layout; ?></h2>
    <?php
        echo $this->Form->create('Locale', array(
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
        echo $this->Form->input('Locale.content', array(
            'label' => __('Content'),
            'value' => $content,
            'type' => 'textarea',
            'class' => 'content',
        ));
    ?>
    </fieldset>

    <div class="buttons">
    <?php
        echo $this->Form->end(__('Save'));
        echo $this->Html->link(__('Cancel'), array(
            'action' => 'index',
        ), array(
            'class' => 'cancel',
        ));
    ?>
    </div>
=======
	<h2><?php echo $title_for_layout; ?></h2>
	<?php
		echo $this->Form->create('Locale', array(
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
		echo $this->Form->input('Locale.content', array(
			'label' => __('Content', true),
			'value' => $content,
			'type' => 'textarea',
			'class' => 'content',
		));
	?>
	</fieldset>

	<div class="buttons">
	<?php
		echo $this->Form->end(__('Save', true));
		echo $this->Html->link(__('Cancel', true), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
>>>>>>> 1.3-whitespace
</div>