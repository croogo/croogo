<div class="node-info">
<?php
	$type = $types_for_layout[$this->Nodes->field('type')];

	if ($type['Type']['format_show_author'] || $type['Type']['format_show_date']) {
		__('Posted');
	}
	if ($type['Type']['format_show_author']) {
		echo ' ' . __('by') . ' ';
		if ($this->Nodes->field('User.website') != null) {
			$author = $this->Html->link($this->Nodes->field('User.name'), $this->Nodes->field('User.website'));
		} else {
			$author = $this->Nodes->field('User.name');
		}
		echo $this->Html->tag('span', $author, array(
			'class' => 'author',
		));
	}
	if ($type['Type']['format_show_date']) {
		echo ' ' . __('on') . ' ';
		echo $this->Html->tag('span', $this->Time->format(Configure::read('Reading.date_time_format'), $this->Nodes->field('created'), null, Configure::read('Site.timezone')), array('class' => 'date'));
	}
?>
</div>