<?php

$rows = [];
foreach ($linkChoosers as $name => $chooser):
    $link = $this->Html->link($name, $chooser['url'], [
        'class' => 'dropdown-item link-chooser',
        'escape' => false,
        'title' => $chooser['description'],
        'data-title' => $name,
        'data-chooser-target' => $target,
        'data-type' => 'Node',
        'data-attr' => 'rel',
        'data-target' => '#link-chooser',
        'data-toggle' => 'modal',
        'data-chooser' => true
    ]);
    $dropdowns[] = $link;
endforeach;

echo $this->Form->button($this->Html->icon('link'), [
    'type' => 'button',
    'class' => 'btn btn-secondary dropdown-toggle',
    'data-toggle' => 'dropdown',
    'aria-haspopup' => true,
    'aria-expanded' => false,
    'escape' => false,
]);
echo $this->Html->div('dropdown-menu dropdown-menu-right', implode($dropdowns));
