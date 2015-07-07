<div id="vocabulary-<?php echo $vocabulary['Vocabulary']['id']; ?>" class="vocabulary">
<?php
	echo $this->Taxonomies->nestedTerms($vocabulary['threaded'], $options);
?>
</div>