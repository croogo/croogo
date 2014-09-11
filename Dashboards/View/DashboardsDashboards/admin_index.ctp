<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php
$this->Croogo->adminScript('Dashboards.admin');
$this->Html->css('Dashboards.admin', array('inline' => false));

$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Dashboard'), '/' . $this->request->url);

echo $this->Dashboards->dashboards();
?>
<div id="dashboard-url" class="hidden"><?php echo $this->Html->url(array('action' => 'save'));?></div>
