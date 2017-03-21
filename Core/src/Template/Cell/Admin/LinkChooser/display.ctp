<?php

use Cake\Utility\Hash;

ksort($linkChoosers);
$rows = [];
foreach ($linkChoosers as $name => $chooser):
    $chooser = Hash::merge([
        'title' => null,
        'description' => null,
        'url' => null,
    ], $chooser);
    $link = $this->Html->link($chooser['title'], $chooser['url'], [
        'class' => 'dropdown-item link-chooser',
        'escape' => false,
        'title' => $chooser['description'],
        'data-title' => $chooser['title'],
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
echo $this->Html->div('dropdown-menu dropdown-menu-right dropdown-scrollable', implode($dropdowns));
