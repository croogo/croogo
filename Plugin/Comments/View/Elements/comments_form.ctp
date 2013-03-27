<div class="comment-form">
	<h3><?php echo __d('croogo', 'Add new comment'); ?></h3>
	<?php
		$type = $types_for_layout[$node['Node']['type']];

		if ($this->params['controller'] == 'comments') {
			$nodeLink = $this->Html->link(__d('croogo', 'Go back to original post') . ': ' . $node['Node']['title'], $node['Node']['url']);
			echo $this->Html->tag('p', $nodeLink, array('class' => 'back'));
		}

		$formUrl = array(
			'plugin' => 'comments',
			'controller' => 'comments',
			'action' => 'add',
			$node['Node']['id'],
		);
		if (isset($parentId) && $parentId != null) {
			$formUrl[] = $parentId;
		}

		echo $this->Form->create('Comment', array('url' => $formUrl));
			if ($this->Session->check('Auth.User.id')) {
				echo $this->Form->input('Comment.name', array(
					'label' => __d('croogo', 'Name'),
					'value' => $this->Session->read('Auth.User.name'),
					'readonly' => 'readonly',
				));
				echo $this->Form->input('Comment.email', array(
					'label' => __d('croogo', 'Email'),
					'value' => $this->Session->read('Auth.User.email'),
					'readonly' => 'readonly',
				));
				echo $this->Form->input('Comment.website', array(
					'label' => __d('croogo', 'Website'),
					'value' => $this->Session->read('Auth.User.website'),
					'readonly' => 'readonly',
				));
				echo $this->Form->input('Comment.body', array('label' => false));
			} else {
				echo $this->Form->input('Comment.name', array('label' => __d('croogo', 'Name')));
				echo $this->Form->input('Comment.email', array('label' => __d('croogo', 'Email')));
				echo $this->Form->input('Comment.website', array('label' => __d('croogo', 'Website')));
				echo $this->Form->input('Comment.body', array('label' => false));
			}

			if ($type['Type']['comment_captcha']) {
				echo $this->Recaptcha->display_form();
			}
		echo $this->Form->end(__d('croogo', 'Post comment'));
	?>
</div>