<?php
$crumbs = $this->Html->getCrumbs(
	$this->Html->tag('span', '/', array(
		'class' => 'divider',
	))
);
?>
<?php if ($crumbs): ?>
<div id="breadcrumb-container" class="span12 visible-desktop">
	<div class="breadcrumb">
		<?php echo $crumbs; ?>
	</div>
</div>
<?php endif; ?>
