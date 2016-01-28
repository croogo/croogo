<?php $this->assign('title', __d('croogo', 'Dashboards')); ?>
<h2 class="hidden-desktop"><?= h($this->fetch('title')); ?></h2>
<?php
$this->Croogo->adminScript('Croogo/Dashboards.admin');
$this->CroogoHtml->css('Croogo/Dashboards.admin', array('block' => true));

use Cake\Routing\Router;

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Dashboard'), '/' . $this->request->url);

echo $this->Dashboards->dashboards();

$this->CroogoHtml->scriptBlock('Dashboard.init();', ['block' => 'scriptBottom']);
?>
<div id="dashboard-url" class="hidden"><?php echo Router::url(array('action' => 'save'));?></div>
