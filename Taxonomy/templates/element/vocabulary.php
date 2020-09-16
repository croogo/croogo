<div id="vocabulary-<?= $vocabulary['vocabulary']->id ?>" class="vocabulary">
<?php
    echo $this->Taxonomies->nestedTerms($vocabulary['threaded'], $options);
?>
</div>
