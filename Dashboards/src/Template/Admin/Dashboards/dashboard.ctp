<?php $this->assign('title', __d('croogo', 'Dashboards')) ?>
<?php
$this->Croogo->adminScript('Croogo/Dashboards.admin');
$this->Html->css('Croogo/Dashboards.admin', ['block' => true]);

$this->Breadcrumbs  ->add(__d('croogo', 'Dashboard'), $this->getRequest()->getRequestTarget());

echo $this->Dashboards->dashboards();

$this->Js->buffer('Dashboard.init();');
?>
<div id="dashboard-url" style="display: none"><?= $this->Url->build(['action' => 'save']);?></div>
