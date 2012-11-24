<div class="row-fluid">
	<div class="span12">
		<div class="row-fluid filter">
			<?php
			echo $this->Form->create('Node', array(
				'class' => 'inline',
				'url' => array_merge(
					array('action' => 'index'), $this->params['pass']
				)
			));

			echo $this->Form->input('filter', array(
				'label' => false,
				'title' => __('Search'),
				'placeholder' => __('Search...'),
				'div' => 'input text span3',
				'class' => 'span11',
				'tooltip' => false,
			));

			echo $this->Form->input('type', array(
				'label' => false,
				'options' => $nodeTypes,
				'empty' => __('Type'),
				'div' => 'input select span2',
				'class' => 'span11'
			));

			echo $this->Form->input('status', array(
				'label' => false,
				'options' => array(
					'1' => __('Published'),
					'0' => __('Unpublished'),
				),
				'empty' => __('Status'),
				'div' => 'input select span2',
				'class' => 'span11'
			));

			echo $this->Form->input('promote', array(
				'label' => false,
				'options' => array(
					'1' => __('Yes'),
					'0' => __('No'),
				),
				'empty' => __('Promoted'),
				'div' => 'input select span2',
				'class' => 'span11'
			));
			echo $this->Form->submit(__('Filter'), array('class' => 'btn',
				'div' => 'input submit span2'
			));
			echo $this->Form->end();
			?>
		</div>
	</div>
</div>
