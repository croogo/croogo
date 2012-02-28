<div class="comments">
<?php
	$commentHeading = $node['Node']['comment_count'] . ' ';
	if ($node['Node']['comment_count'] == 1) {
		$commentHeading .= __('Comment');
	} else {
		$commentHeading .= __('Comments');
	}
	echo $this->Html->tag('h3', $commentHeading);

	foreach ($comments AS $comment) {
		echo $this->element('comment', array('comment' => $comment, 'level' => 1));
	}
?>
</div>