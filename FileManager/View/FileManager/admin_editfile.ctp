<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'File Manager'), array('plugin' => 'file_manager', 'controller' => 'file_manager', 'action' => 'browse'))
	->addCrumb(basename($absolutefilepath), '/' . $this->request->url);

echo $this->Form->create('FileManager', array(
	'url' => $this->Html->url(array(
		'controller' => 'file_manager',
		'action' => 'editfile',
	), true) . '?path=' . urlencode($absolutefilepath),
));
?>
<h2 class="hidden-desktop"><?php echo __d('croogo', 'Edit file'); ?> </h2>
<div class="breadcrumb">
	<a href="#"><?php echo __d('croogo', 'You are here') . ' '; ?> </a> <span class="divider"> &gt; </span>
	<?php $breadcrumb = $this->FileManager->breadcrumb($path); ?>
	<?php foreach ($breadcrumb as $pathname => $p) : ?>
		<?php echo $this->FileManager->linkDirectory($pathname, $p); ?>
		<span class="divider"> <?php echo DS; ?> </span>
	<?php endforeach; ?>
	</ul>
</div>

&nbsp;

<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Edit'), '#filemanager-edit');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<div id="filemanager-edit" class="tab-pane">
			<?php
				echo $this->Form->input('FileManager.content', array(
					'type' => 'textarea',
					'value' => $content,
					'class' => 'span12',
					'label' => false,
				));
			?>
			</div>
			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>
	<div class="span4">
		<?php
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Html->endBox();
		?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
