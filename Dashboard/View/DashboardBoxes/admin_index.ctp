<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Dashboard'), '/' . $this->request->url);

echo $this->Dashboard->adminDashboard();
?>