<?php $this->extend('/Common/admin_edit'); ?>
<?php $this->Html->script(array('nodes'), false); ?>
<?php echo $this->Form->create('Node', array('url' => array('action' => 'edit')));?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#node-main"><span><?php echo __($type['Type']['title']); ?></span></a></li>
				<?php if (count($taxonomy) > 0) { ?><li><a href="#node-terms"><span><?php echo __('Terms'); ?></span></a></li><?php } ?>
				<?php if ($type['Type']['comment_status'] != 0) { ?><li><a href="#node-comments"><span><?php echo __('Comments'); ?></span></a></li><?php } ?>
				<li><a href="#node-meta"><span><?php echo __('Custom fields'); ?></span></a></li>
				<li><a href="#node-access"><span><?php echo __('Access'); ?></span></a></li>
				<li><a href="#node-publishing"><span><?php echo __('Publishing'); ?></span></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>

			<div id="node-main">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('parent_id', array('type' => 'select', 'options' => $nodes, 'empty' => true));
				echo $this->Form->input('title');
				echo $this->Form->input('slug');
				echo $this->Form->input('excerpt');
				echo $this->Form->input('body', array('class' => 'content'));
			?>
			</div>

			<?php if (count($taxonomy) > 0) { ?>
			<div id="node-terms">
			<?php
				$taxonomyIds = Set::extract('/Taxonomy/id', $this->data);
				foreach ($taxonomy AS $vocabularyId => $taxonomyTree) {
					echo $this->Form->input('TaxonomyData.'.$vocabularyId, array(
						'label' => $vocabularies[$vocabularyId]['title'],
						'type' => 'select',
						'multiple' => true,
						'options' => $taxonomyTree,
						'value' => $taxonomyIds,
					));
				}
			?>
			</div>
			<?php } ?>

			<?php if ($type['Type']['comment_status'] != 0) { ?>
			<div id="node-comments">
			<?php
				echo $this->Form->input('comment_status', array(
					'type' => 'radio',
					'div' => array('class' => 'radio'),
					'options' => array(
						'0' => __('Disabled'),
						'1' => __('Read only'),
						'2' => __('Read/Write'),
					),
				));
			?>
			</div>
			<?php } ?>

			<div id="node-meta">
				<div id="meta-fields">
					<?php
						$fields = Set::combine($this->data['Meta'], '{n}.key', '{n}.value');
						$fieldsKeyToId = Set::combine($this->data['Meta'], '{n}.key', '{n}.id');
						if (count($fields) > 0) {
							foreach ($fields AS $fieldKey => $fieldValue) {
								echo $this->Layout->metaField($fieldKey, $fieldValue, $fieldsKeyToId[$fieldKey]);
							}
						}
					?>
					<div class="clear">&nbsp;</div>
				</div>
				<?php echo $this->Html->link(
					__('Add another field'),
					array('action' => 'add_meta'),
					array('class' => 'add-meta')
				); ?>
			</div>

			<div id="node-access">
				<?php
					echo $this->Form->input('Role.Role');
				?>
			</div>

			<div id="node-publishing">
			<?php
				echo $this->Form->input('status', array('label' => __('Published')));
				echo $this->Form->input('promote', array('label' => __('Promoted to front page')));
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
<?php echo $this->Form->end();?>