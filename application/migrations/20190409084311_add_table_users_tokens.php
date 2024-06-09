<?php


use Phinx\Migration\AbstractMigration;

class AddTableUsersTokens extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users_tokens');
        $table->addColumn('type', 'text')
              ->addColumn('token','text')
              ->addColumn('user_id','integer',['null' => FALSE])
              ->addColumn('created_on', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}
