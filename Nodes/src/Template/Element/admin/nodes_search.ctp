<?php

$url = isset($url) ? $url : array('action' => 'index');

?>
<div class="clearfix filter">
<?php
	echo $this->CroogoForm->create(false, array(
		'class' => 'form-inline',
		'url' => $url
	));

	$this->CroogoForm->templates(array(
		'label' => false,
		'class' => 'span11',
		'submitContainer' => '<div class="input submit span2">{{content}}</div>'
	));

	echo $this->CroogoForm->input('filter', array(
		'title' => __d('croogo', 'Search'),
		'placeholder' => __d('croogo', 'Search...'),
		'tooltip' => false,
	));

	if (!isset($this->request->query['chooser'])):

		echo $this->CroogoForm->input('type', array(
			'options' => $nodeTypes,
			'empty' => __d('croogo', 'Type'),
		));

		echo $this->CroogoForm->input('status', array(
			'options' => array(
				'1' => __d('croogo', 'Published'),
				'0' => __d('croogo', 'Unpublished'),
			),
			'empty' => __d('croogo', 'Status'),
		));

		echo $this->CroogoForm->input('promote', array(
			'options' => array(
				'1' => __d('croogo', 'Yes'),
				'0' => __d('croogo', 'No'),
			),
			'empty' => __d('croogo', 'Promoted'),
		));

	endif;

	echo $this->CroogoForm->input(__d('croogo', 'Filter'), [
		'type' => 'submit'
	]);
	echo $this->CroogoForm->end();
?>
</div>
