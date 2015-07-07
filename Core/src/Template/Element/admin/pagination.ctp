<div class="pagination">
	<p>
	<?php
		echo $this->Paginator->counter(array(
			'format' => __d('croogo', 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
		));
	?>
	</p>
	<ul>
		<?php echo $this->Paginator->first('< ' . __d('croogo', 'first')); ?>
		<?php echo $this->Paginator->prev('< ' . __d('croogo', 'prev')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__d('croogo', 'next') . ' >'); ?>
		<?php echo $this->Paginator->last(__d('croogo', 'last') . ' >'); ?>
	</ul>
</div>
