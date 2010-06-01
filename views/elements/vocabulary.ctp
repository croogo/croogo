<div id="vocabulary-<?php echo $vocabulary['Vocabulary']['id']; ?>" class="vocabulary">
<?php
    echo $layout->nestedTerms($vocabulary['threaded'], $options);
?>
</div>