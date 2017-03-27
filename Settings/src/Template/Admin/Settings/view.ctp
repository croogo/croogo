<div class="settings view">
<h2><?= __d('croogo', 'Setting'); ?></h2>
    <dl><?php $i = 0; $class = ' class="altrow"';?>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?= __d('croogo', 'Id'); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?= $setting->id; ?>
            &nbsp;
        </dd>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?= __d('croogo', 'Key'); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?= $setting->key; ?>
            &nbsp;
        </dd>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?= __d('croogo', 'Value'); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?= $setting->value; ?>
            &nbsp;
        </dd>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?= __d('croogo', 'Description'); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?= $setting->description; ?>
            &nbsp;
        </dd>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?= __d('croogo', 'Input Type'); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?= $setting->input_type; ?>
            &nbsp;
        </dd>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?= __d('croogo', 'Weight'); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?= $setting->weight; ?>
            &nbsp;
        </dd>
        <dt<?php if ($i % 2 == 0) echo $class;?>><?= __d('croogo', 'Params'); ?></dt>
        <dd<?php if ($i++ % 2 == 0) echo $class;?>>
            <?= $setting->params; ?>
            &nbsp;
        </dd>
    </dl>
</div>
<div class="actions">
    <ul>
        <li><?= $this->Html->link(__d('croogo', 'Edit Setting'), ['action' => 'edit', $setting->id]); ?> </li>
        <li><?= $this->Form->postLink(__d('croogo', 'Delete Setting'), ['action' => 'delete', $setting->id], ['confirm' => __d('croogo', 'Are you sure you want to delete # %s?', $setting->id)]); ?> </li>
        <li><?= $this->Html->link(__d('croogo', 'List Settings'), ['action' => 'index']); ?> </li>
        <li><?= $this->Html->link(__d('croogo', 'New Setting'), ['action' => 'add']); ?> </li>
    </ul>
</div>
