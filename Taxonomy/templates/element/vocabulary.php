<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $options
 * @var array $vocabulary
 */
?>
<div id="vocabulary-<?= $vocabulary['vocabulary']->id ?>" class="vocabulary">
<?php
    echo $this->Taxonomies->nestedTerms($vocabulary['threaded'], $options);
?>
</div>
