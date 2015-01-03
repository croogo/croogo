<?php
$crumbs = $this->Html->getCrumbs(
	$this->Html->tag('span', '/', array(
		'class' => 'divider',
	))
);
?>
<?php if ($crumbs): ?>
<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
	<div id="breadcrumb-container" class="<?php echo $this->Theme->getCssClass('columnFull'); ?> <?php echo $this->Theme->getCssClass('visibleDesktop'); ?>">
		<div class="breadcrumb">
			<?php echo $crumbs; ?>
		</div>
	</div>
</div>
<?php endif; ?>
