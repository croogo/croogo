<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('File Manager'), array('plugin' => 'file_manager', 'controller' => 'file_manager', 'action' => 'browse'))
	->addCrumb(__('Create Directory'), $this->here);

echo $this->Form->create('FileManager', array(
	'url' => $this->Html->url(array(
		'controller' => 'file_manager',
		'action' => 'create_directory',
	), true) . '?path=' . urlencode($path),
));
?>
<h2 class="hidden-desktop"><?php echo __('Create Directory'); ?></h2>
<div class="breadcrumb">
	<a href="#"><?php echo __('You are here') . ' '; ?> </a> <span class="divider"> &gt; </span>
	<?php $breadcrumb = $this->FileManager->breadcrumb($path); ?>
	<?php foreach ($breadcrumb as $pathname => $p) : ?>
		<?php echo $this->FileManager->linkDirectory($pathname, $p); ?>
			<span class="divider"> <?php echo DS;  ?> </span>
	<?php endforeach; ?>
</div>

&nbsp;

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#filemanager-createdir" data-toggle="tab"><?php echo __('Directory'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div id="filemanager-createdir" class="tab-pane">
			<?php
				echo $this->Form->input('FileManager.name', array(
					'type' => 'text',
					'placeholder' => __('Directory name'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Create'), array('button' => 'default')) .
			$this->Html->link(__('Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
