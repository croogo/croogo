<?php

extract((array)$this->getRequest()->getQuery());
if (empty($model) || empty($foreign_key)) :
    return;
endif;

echo $this->element('Croogo/FileManager.admin/asset_list', [
    'model' => $model,
    'foreignKey' => $foreign_key,
    'attachments' => $attachments,
]);
