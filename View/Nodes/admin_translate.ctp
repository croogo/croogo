<?php
	$this->Html->script(array('nodes'), false);
	if (Configure::read('Writing.wysiwyg')) {
		$this->Html->scriptBlock($tinymce->fileBrowserCallBack(), array('inline' => false));
		$this->Html->scriptBlock($tinymce->init('NodeBody'), array('inline' => false));
	}
?>
<div class="nodes form">
	<h2><?php echo $title_for_layout; ?></h2>

<<<<<<< HEAD
	<?php
		echo $this->Form->create('Node', array('url' => array(
			'action' => 'translate',
			'locale' => $this->params['named']['locale'],
		)));
	?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#node-main"><span><?php echo __($type['Type']['title']); ?></span></a></li>
			</ul>
=======
	<?php
		echo $this->Form->create('Node', array('url' => array(
			'action' => 'translate',
			'locale' => $this->params['named']['locale'],
		)));
	?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#node-main"><span><?php __($type['Type']['title']); ?></span></a></li>
			</ul>
>>>>>>> 1.3-whitespace

			<div id="node-main">
			<?php
				foreach ($fields AS $field) {
					echo $this->Form->input('Node.'.$field);
				}
			 ?>
			 </div>
		</div>
	</fieldset>
	<?php echo $this->Form->end('Submit');?>
</div>