<div class="extensions-themes">
<<<<<<< HEAD
    <h2><?php echo $title_for_layout; ?></h2>
    <?php
        echo $this->Form->create('Theme', array(
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
        echo $this->Form->input('Theme.file', array('label' => __('Upload'), 'type' => 'file',));
    ?>
    </fieldset>

    <div class="buttons">
    <?php
        echo $this->Form->end(__('Upload'));
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
		echo $this->Form->create('Theme', array(
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
		echo $this->Form->input('Theme.file', array('label' => __('Upload', true), 'type' => 'file',));
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
>>>>>>> 1.3-whitespace
</div>