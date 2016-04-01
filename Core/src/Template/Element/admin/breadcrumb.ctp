<?php
$crumbs = $this->Html->getCrumbList([
    'firstClass' => '',
    'lastClass' => ''
], [
    'text' => '',
    'text' => '',
    'url' => '/admin',
    'icon' => 'home',
    'escape' => false
]);
?>
<?php if ($crumbs): ?>
<div id="breadcrumb-container" class="col-xs-12 visible-md-up">
    <?php echo $crumbs; ?>
</div>
<?php endif; ?>
