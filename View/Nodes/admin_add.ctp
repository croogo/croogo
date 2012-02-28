<?php $this->Html->script(array('nodes'), false); ?>
<div class="nodes form">
<<<<<<< HEAD
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $this->Form->create('Node', array('url' => array('action' => 'add', $typeAlias)));?>
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
=======
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('Node', array('url' => array('action' => 'add', $typeAlias)));?>
		<fieldset>
			<div class="tabs">
				<ul>
					<li><a href="#node-main"><span><?php __($type['Type']['title']); ?></span></a></li>
					<?php if (count($taxonomy) > 0) { ?><li><a href="#node-terms"><span><?php __('Terms'); ?></span></a></li><?php } ?>
					<?php if ($type['Type']['comment_status'] != 0) { ?><li><a href="#node-comments"><span><?php __('Comments'); ?></span></a></li><?php } ?>
					<li><a href="#node-meta"><span><?php __('Custom fields'); ?></span></a></li>
					<li><a href="#node-access"><span><?php __('Access'); ?></span></a></li>
					<li><a href="#node-publishing"><span><?php __('Publishing'); ?></span></a></li>
					<?php echo $this->Layout->adminTabs(); ?>
				</ul>
>>>>>>> 1.3-whitespace

				<div id="node-main">
				<?php
					echo $this->Form->input('parent_id', array('type' => 'select', 'options' => $nodes, 'empty' => true));
					echo $this->Form->input('title');
					echo $this->Form->input('slug', array('class' => 'slug'));
					echo $this->Form->input('excerpt');
					echo $this->Form->input('body', array('class' => 'content'));
				?>
				</div>

				<?php if (count($taxonomy) > 0) { ?>
				<div id="node-terms">
				<?php
					foreach ($taxonomy AS $vocabularyId => $taxonomyTree) {
						echo $this->Form->input('TaxonomyData.'.$vocabularyId, array(
							'label' => $vocabularies[$vocabularyId]['title'],
							'type' => 'select',
							'multiple' => true,
							'options' => $taxonomyTree,
						));
					}
				?>
				</div>
				<?php } ?>

<<<<<<< HEAD
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
                        'value' => $type['Type']['comment_status'],
                    ));
                ?>
                </div>
                <?php } ?>

                <div id="node-meta">
                    <div id="meta-fields">
                        <?php
                            $fields = array();
                            if (count($fields) > 0) {
                                foreach ($fields AS $fieldKey => $fieldValue) {
                                    echo $this->Layout->metaField($fieldKey, $fieldValue);
                                }
                            } else {
                                echo $this->Layout->metaField();
                            }
                        ?>
                        <div class="clear">&nbsp;</div>
                    </div>
                    <a href="#" class="add-meta"><?php echo __('Add another field'); ?></a>
                </div>
=======
				<?php if ($type['Type']['comment_status'] != 0) { ?>
				<div id="node-comments">
				<?php
					echo $this->Form->input('comment_status', array(
						'type' => 'radio',
						'div' => array('class' => 'radio'),
						'options' => array(
							'0' => __('Disabled', true),
							'1' => __('Read only', true),
							'2' => __('Read/Write', true),
						),
						'value' => $type['Type']['comment_status'],
					));
				?>
				</div>
				<?php } ?>

				<div id="node-meta">
					<div id="meta-fields">
						<?php
							$fields = array();
							if (count($fields) > 0) {
								foreach ($fields AS $fieldKey => $fieldValue) {
									echo $this->Layout->metaField($fieldKey, $fieldValue);
								}
							} else {
								echo $this->Layout->metaField();
							}
						?>
						<div class="clear">&nbsp;</div>
					</div>
					<?php echo $this->Html->link(
						__('Add another field', true),
						array('action' => 'add_meta'),
						array('class' => 'add-meta')
					); ?>
				</div>
>>>>>>> 1.3-whitespace

				<div id="node-access">
					<?php
						echo $this->Form->input('Role.Role');
					?>
				</div>

<<<<<<< HEAD
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
    <?php
        echo $this->Form->input('token_key', array(
            'type' => 'hidden',
            'value' => $this->params['_Token']['key'],
        ));
    ?>
    <div class="buttons">
    <?php
        echo $this->Form->submit(__('Apply'), array('name' => 'apply'));
        echo $this->Form->end(__('Save'));
        echo $this->Html->link(__('Cancel'), array(
            'action' => 'index',
        ), array(
            'class' => 'cancel',
        ));
    ?>
    </div>
=======
				<div id="node-publishing">
				<?php
					echo $this->Form->input('status', array(
						'label' => __('Published', true),
						'checked' => 'checked',
					));
					echo $this->Form->input('promote', array(
						'label' => __('Promoted to front page', true),
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
	<?php
		echo $this->Form->input('token_key', array(
			'type' => 'hidden',
			'value' => $this->params['_Token']['key'],
		));
	?>
	<div class="buttons">
	<?php
		echo $this->Form->submit(__('Apply', true), array('name' => 'apply'));
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