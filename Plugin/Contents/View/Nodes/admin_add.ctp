<?php $this->extend('/Common/admin_edit'); ?>
<?php $this->Html->script(array('nodes'), false); ?>
<?php echo $this->Form->create('Node', array('url' => array('action' => 'add', $typeAlias)));?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#node-main"><span><?php echo __($type['Type']['title']); ?></span></a></li>
				<li><a href="#node-access"><span><?php echo __('Access'); ?></span></a></li>
				<li><a href="#node-publishing"><span><?php echo __('Publishing'); ?></span></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>

			<div id="node-main">
			<?php
				echo $this->Form->input('parent_id', array('type' => 'select', 'options' => $nodes, 'empty' => true));
				echo $this->Form->input('title');
				echo $this->Form->input('slug', array('class' => 'slug'));
				echo $this->Form->input('excerpt');
				echo $this->Form->input('body', array('class' => 'content'));
			?>
			</div>

			<div id="node-access">
				<?php
					echo $this->Form->input('Role.Role');
				?>
			</div>

			<div id="node-publishing">
			<?php
				echo $this->Form->input('status', array(
					'label' => __('Published'),
					'checked' => 'checked',
				));
				echo $this->Form->input('promote', array(
					'label' => __('Promoted to front page'),
					'checked' => 'checked',
				));
				echo $this->Form->input('user_id');
				echo $this->Form->input('created');
			?>
			</div>
			<?php echo $this->Layout->adminTabs(); ?>
			<div class="clear">&nbsp;</div>
		</div>
	</fieldset>
	<div class="buttons">
	<?php
		echo $this->Form->submit(__('Apply'), array('name' => 'apply'));
		echo $this->Form->submit(__('Save'));
		echo $this->Html->link(__('Cancel'), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
<?php echo $this->Form->end(); ?>