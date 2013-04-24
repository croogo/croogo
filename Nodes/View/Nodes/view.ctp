<?php $this->Nodes->set($node); ?>
<div id="node-<?php echo $this->Nodes->field('id'); ?>" class="node node-type-<?php echo $this->Nodes->field('type'); ?>">
	<h2><?php echo $this->Nodes->field('title'); ?></h2>
	<?php
		echo $this->Nodes->info();
		echo $this->Nodes->body();
		echo $this->Nodes->moreInfo();
	?>
</div>

<div id="comments" class="node-comments">
<?php
	$type = $types_for_layout[$this->Nodes->field('type')];

	if ($type['Type']['comment_status'] > 0 && $this->Nodes->field('comment_status') > 0) {
		echo $this->element('Comments.comments');
	}

	if ($type['Type']['comment_status'] == 2 && $this->Nodes->field('comment_status') == 2) {
		echo $this->element('Comments.comments_form');
	}
?>
</div>