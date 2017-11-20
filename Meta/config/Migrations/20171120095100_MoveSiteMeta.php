<?php

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Migrations\AbstractMigration;

class MoveSiteMeta extends AbstractMigration
{
    public function up()
    {
        if (Configure::check('Meta')) {
            $settingsTable = TableRegistry::get('Croogo/Settings.Settings');
            $meta = Configure::read('Meta');
            $settingsTable = TableRegistry::get('Croogo/Settings.Settings');
            $metaTable = TableRegistry::get('Croogo/Meta.Meta');

            $newMetas = [];
            foreach ($meta as $key => $value) {
                $newMetas[] = $metaTable->newEntity([
                    'model' => '',
                    'key' => $key,
                    'value' => $value
                ]);

                $settingsTable->deleteKey('Meta.' . $key);
            }
            $metaTable->saveMany($newMetas);
        }
    }

    public function down()
    {
    }
}
