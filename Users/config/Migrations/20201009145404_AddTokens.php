<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddTokens extends AbstractMigration
{
    public function up()
    {
        $this->table('users')
            ->addColumn('token', 'string', [
                'default' => null,
                'limit' => 60,
                'null' => true,
            ])
            ->addColumn('jwt', 'string', [
                'default' => null,
                'limit' => 512,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('users')
            ->removeColumn('token')
            ->removeColumn('jwt')
            ->update();
    }
}
