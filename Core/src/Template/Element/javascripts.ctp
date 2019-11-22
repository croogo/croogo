<?php
$this->Html->script([
    'jquery/jquery.min',
], [
    'block' => true,
]);

$this->Html->script([
    'core/bootstrap.min',
    'core/popper.min',
    'jquery-easing/jquery.easing.min',
], [
    'block' => true,
    'async' => true,
]);

$this->Html->script([
    'theme',
], [
    'defer' => true,
]);
