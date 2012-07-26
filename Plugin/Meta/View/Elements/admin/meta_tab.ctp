<div id="meta-fields">
<?php
	if (!empty($this->data['Meta'])) {
		$fields = Set::combine($this->data['Meta'], '{n}.key', '{n}.value');
		$fieldsKeyToId = Set::combine($this->data['Meta'], '{n}.key', '{n}.id');
	} else {
		$fields = $fieldsKeyToId = array();
	}
	if (count($fields) > 0) {
		foreach ($fields AS $fieldKey => $fieldValue) {
			echo $this->Meta->field($fieldKey, $fieldValue, $fieldsKeyToId[$fieldKey]);
		}
	}
?>
	<div class="clear">&nbsp;</div>
</div>
<?php
echo $this->Html->link(
	__('Add another field'),
	array('action' => 'add_meta'),
	array('class' => 'add-meta')
);
?>
