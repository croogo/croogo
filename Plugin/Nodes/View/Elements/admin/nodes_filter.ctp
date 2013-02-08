<?php
if (!isset($url)) {
	$url = array_merge(
		array('action' => 'index'), $this->request->params['pass']
	);
}
?>
<div class="row-fluid">
	<div class="span12">
		<div class="clearfix filter">
			<?php
			echo $this->Form->create('Node', array(
				'class' => 'inline',
				'url' => $url,
			));

			$this->Form->inputDefaults(array(
				'label' => false,
				'class' => 'span11',
			));

			echo $this->Form->input('filter', array(
				'title' => __('Search'),
				'placeholder' => __('Search...'),
				'div' => 'input text span3',
				'tooltip' => false,
			));

			echo $this->Form->input('type', array(
				'options' => $nodeTypes,
				'empty' => __('Type'),
				'div' => 'input select span2',
			));

			echo $this->Form->input('status', array(
				'options' => array(
					'1' => __('Published'),
					'0' => __('Unpublished'),
				),
				'empty' => __('Status'),
				'div' => 'input select span2',
			));

			echo $this->Form->input('promote', array(
				'options' => array(
					'1' => __('Yes'),
					'0' => __('No'),
				),
				'empty' => __('Promoted'),
				'div' => 'input select span2',
			));
			echo $this->Form->submit(__('Filter'), array('class' => 'btn',
				'div' => 'input submit span2'
			));
			echo $this->Form->end();
			?>
		</div>
	</div>
</div>
