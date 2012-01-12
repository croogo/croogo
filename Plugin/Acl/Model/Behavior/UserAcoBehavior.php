<?php

class UserAcoBehavior extends ModelBehavior {

    public function parentNode($model) {
        if (!$model->id && empty($model->data)) {
            return null;
        }
        $data = $model->data;
        if (empty($model->data)) {
            $data = $model->read();
        }
        if (!isset($data['User']['role_id']) || !$data['User']['role_id']) {
            return null;
        } else {
            return array('Role' => array('id' => $data['User']['role_id']));
        }
    }

    public function afterSave($model, $created) {
        if (!$created) {
            $parent = $model->parentNode();
            $parent = $model->node($parent);
            $node = $model->node();
            $aro = $node[0];
            $aro['Aro']['parent_id'] = $parent[0]['Aro']['id'];
            $model->Aro->save($aro);
        }
    }

}
