<?php

use Cake\Core\Configure;
use Cake\Error\Debugger;

if ($this->getRequest()->getParam('prefix') === 'Admin'):
    $this->setLayout('admin_error');
else:
    $this->setLayout('error');
endif;

?>
<div class="text-center">
<h6><?= __d('cake', 'A security error has occurred') ?></h6>
<p class="error">
    <?php if (Configure::read('debug')): ?>
    <strong><?= __d('cake', 'Error') ?>: </strong>
    <?= h($message) ?>
    <?php endif ?>
</p>
</div>
