<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Locales'), array('plugin' => 'extensions', 'controller' => 'extensions_locales', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Upload'), '/' . $this->request->url);

echo $this->Form->create('Locale', array(
	'url' => array(
		'plugin' => 'extensions',
		'controller' => 'extensions_locales',
		'action' => 'add',
	),
	'type' => 'file',
));

?>
<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#locale-upload');
		?>
		</ul>

		<div class="tab-content">
			<div id="locale-upload" class="tab-pane">
			<?php
				echo $this->Form->input('Locale.file', array(
					'type' => 'file',
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>

	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Upload'), array('button' => 'default')) .
			$this->Form->end() .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
