<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php
$this->Croogo->adminScript('Dashboards.admin');
$this->Html->css('Dashboards.admin', array('inline' => false));

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Dashboard'), '/' . $this->request->url);

echo $this->Dashboards->dashboards();

$this->Js->buffer('Dashboard.init();');
?>
<div id="dashboard-url" class="hidden"><?php echo $this->Html->url(array('action' => 'save'));?></div>
