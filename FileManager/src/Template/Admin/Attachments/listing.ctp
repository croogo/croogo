<?php

extract((array)$this->request->getQuery());
if (empty($model) || empty($foreign_key)):
    return;
endif;

echo $this->element('Croogo/FileManager.admin/asset_list', array(
    'model' => $model,
    'foreignKey' => $foreign_key,
    'attachments' => $attachments,
));
