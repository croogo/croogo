<?php
use Migrations\AbstractMigration;

class DefaultPasswordValue extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('users');
        $table->changeColumn('password', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->update();
    }
}
