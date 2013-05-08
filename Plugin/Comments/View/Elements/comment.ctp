<div id="comment-<?php echo $comment['Comment']['id']; ?>" class="comment<?php if ($node['Node']['user_id'] == $comment['Comment']['user_id']) { echo ' author'; } ?>">
	<div class="comment-info">
		<span class="avatar"><?php echo $this->Html->image('http://www.gravatar.com/avatar/' . md5(strtolower($comment['Comment']['email'])) . '?s=32') ?></span>
		<span class="name">
		<?php
			if ($comment['Comment']['website'] != null) {
				echo $this->Html->link($comment['Comment']['name'], $comment['Comment']['website'], array('target' => '_blank'));
			} else {
				echo $comment['Comment']['name'];
			}
		?>
		</span>
		<span class="date"><?php echo __d('croogo', 'said on %s', $this->Time->format(Configure::read('Comment.date_time_format'), $comment['Comment']['created'], null, Configure::read('Site.timezone'))); ?></span>
	</div>
	<div class="comment-body"><?php echo nl2br($this->Text->autoLink($comment['Comment']['body'])); ?></div>
	<div class="comment-reply">
	<?php
		if ($level <= Configure::read('Comment.level')) {
			echo $this->Html->link(__d('croogo', 'Reply'), array(
				'plugin' => 'comments',
				'controller' => 'comments',
				'action' => 'add',
				$node['Node']['id'],
				$comment['Comment']['id'],
			));
		}
	?>
	</div>

	<?php
		if (isset($comment['children']) && count($comment['children']) > 0) {
			foreach ($comment['children'] as $childComment) {
				echo $this->element('Comments.comment', array('comment' => $childComment, 'level' => $level + 1));
			}
		}
	?>
</div>