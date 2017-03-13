<?php
/**
 * @var \uAfrica\View\AppView $this
 */
$steps = [];

foreach (\Croogo\Install\Controller\InstallController::STEPS as $key => $step) {
    if ($onStep >= $key + 1) {
        $options = ['class' => 'btn btn-primary btn-circle'];
    } else {
        $options = ['class' => 'btn btn-secondary btn-circle'];
    }

    $stepButton = $this->Html->tag('span', $key + 1, $options);
    $step = $this->Html->para('', $step);
    $steps[] = $this->Html->div('wizard-step', $stepButton . $step);
}

echo $this->Html->div('wizard-row', implode('', $steps));
