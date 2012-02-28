<?php $this->Html->script(array('links'), false); ?>
<div class="links form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('Link', array('url' => array('controller' => 'links', 'action' => 'edit', 'menu' => $menuId)));?>
		<fieldset>
			<div class="tabs">
				<ul>
					<li><a href="#link-basic"><span><?php __('Link'); ?></span></a></li>
					<li><a href="#link-access"><span><?php __('Access'); ?></span></a></li>
					<li><a href="#link-misc"><span><?php __('Misc.'); ?></span></a></li>
					<?php echo $this->Layout->adminTabs(); ?>
				</ul>

				<div id="link-basic">
					<?php
						echo $this->Form->input('id');
						echo $this->Form->input('menu_id');
						echo $this->Form->input('parent_id', array(
							'label' => __('Parent', true),
							'options' => $parentLinks,
							'empty' => true,
						));
						echo $this->Form->input('title');
						echo $this->Form->input('link') .
							$this->Html->link(__('Link to a Node', true), Router::url(array(
								'controller' => 'nodes',
								'action' => 'index',
								'links' => 1,
							), true) . '?KeepThis=true&TB_iframe=true&height=400&width=600',
							array(
								'class' => 'thickbox',
							));
						echo $this->Form->input('status');
					?>
				</div>

				<div id="link-access">
					<?php
						echo $this->Form->input('Role.Role');
					?>
				</div>

				<div id="link-misc">
					<?php
						echo $this->Form->input('class', array('class' => 'class'));
						echo $this->Form->input('description');
						echo $this->Form->input('rel');
						echo $this->Form->input('target');
						echo $this->Form->input('params');
					?>
				</div>
				<?php echo $this->Layout->adminTabs(); ?>
			</div>
		</fieldset>
	<div class="buttons">
	<?php
		echo $this->Form->end(__('Save', true));
		echo $this->Html->link(__('Cancel', true), array(
			'action' => 'index',
			$menuId,
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
</div>