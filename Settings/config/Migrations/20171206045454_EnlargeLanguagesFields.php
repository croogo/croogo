<?php
use Migrations\AbstractMigration;

class EnlargeLanguagesFields extends AbstractMigration
{

    public function up()
    {
        $this->table('languages')
            ->changeColumn('locale', 'string', [
                'limit' => 15,
            ])
            ->addIndex(['locale'], [
                'name' => 'ix_languages_locale',
                'unique' => true,
            ])
            ->save();
    }

    public function down()
    {
        $this->table('languages')
            ->changeColumn('locale', 'string', [
                'limit' => 5,
            ])
            ->removeIndexByName('ix_languages_locale')
            ->save();
    }

}
