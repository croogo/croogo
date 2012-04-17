<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'translate');
?>
<?php
	echo $this->Form->create($modelAlias, array('url' => array(
		'controller' => 'translate',
		'action' => 'edit',
		$id,
		$modelAlias,
		'locale' => $this->params['named']['locale'],
	)));
?>
<fieldset>
	<div class="tabs">
		<ul>
			<li><a href="#record-main"><span><?php echo __('Record'); ?></span></a></li>
		</ul>

		<div id="record-main">
		<?php
			foreach ($fields AS $field) {
				echo $this->Form->input($modelAlias.'.'.$field);
			}
		 ?>
		 </div>
	</div>
</fieldset>

<div class="buttons">
<?php
	echo $this->Form->end(__('Save'));
?>
</div>