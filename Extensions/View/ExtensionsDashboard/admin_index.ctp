<h2 class="<?php echo $this->Theme->getCssClass('hiddenPhone'); ?>"><?php echo $title_for_layout; ?></h2>
<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Dashboard'), '/' . $this->request->url);
?>