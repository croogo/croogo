<?php
$this->Html->script([
    'jquery/jquery.min',
    'core/bootstrap.min',
], [
    'block' => true,
]);

$this->Html->script([
    'core/popper.min',
    'jquery-easing/jquery.easing.min',
], [
    'block' => true,
    'async' => true,
]);

$this->Html->script([
    'theme',
], [
    'block' => true,
    'defer' => true,
]);
