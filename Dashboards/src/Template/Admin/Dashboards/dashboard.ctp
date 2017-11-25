<?php $this->assign('title', __d('croogo', 'Dashboards')) ?>
<?php
$this->Croogo->adminScript('Croogo/Dashboards.admin');
$this->Html->css('Croogo/Dashboards.admin', array('block' => true));

$this->Breadcrumbs  ->add(__d('croogo', 'Dashboard'), $this->request->getRequestTarget());

echo $this->Dashboards->dashboards();

$this->Html->scriptBlock('Dashboard.init();', ['block' => 'scriptBottom']);
?>
<div id="dashboard-url" class="hidden"><?= $this->Url->build(array('action' => 'save'));?></div>
