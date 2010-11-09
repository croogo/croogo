<div id="vocabulary-<?php echo $vocabulary['Vocabulary']['id']; ?>" class="vocabulary">
<?php
    echo $this->Layout->nestedTerms($vocabulary['threaded'], $options);
?>
</div>