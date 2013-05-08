<div class="install">
	<h2><?php echo $title_for_layout; ?></h2>

	<p>
	<?php
	echo __d('croogo', 'Create tables and load initial data');
	?>
	</p>
</div>
<div class="form-actions">
<?php
echo $this->Html->link(__d('croogo', 'Build database'), array(
	'plugin' => 'install',
	'controller' => 'install',
	'action' => 'data',
	'run' => 1,
), array(
	'tooltip' => array(
		'data-title' => __d('croogo', 'Click here to build your database'),
		'data-placement' => 'left',
	),
	'button' => 'success',
	'icon' => 'none',
	'onclick' => '$(this).find(\'i\').addClass(\'icon-spin icon-spinner\');',
));
?>
</div>