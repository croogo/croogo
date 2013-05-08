<div id="meta-fields">
<?php
	if (!empty($this->data['Meta'])) {
		$fields = Hash::combine($this->data['Meta'], '{n}.key', '{n}.value');
		$fieldsKeyToId = Hash::combine($this->data['Meta'], '{n}.key', '{n}.id');
	} else {
		$fields = $fieldsKeyToId = array();
	}
	if (count($fields) > 0) {
		foreach ($fields as $fieldKey => $fieldValue) {
			echo $this->Meta->field($fieldKey, $fieldValue, $fieldsKeyToId[$fieldKey]);
		}
	}
?>
	<div class="clear"></div>
</div>
<?php
echo $this->Html->link(
	__d('croogo', 'Add another field'),
	array('action' => 'add_meta'),
	array('class' => 'add-meta')
);
?>
