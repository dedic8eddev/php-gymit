<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableUsersCards extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users_cards');
        $table->addColumn('user_id','integer')
              ->addColumn('card_id','text',['limit' => MysqlAdapter::TEXT_LONG, 'null' => TRUE, 'default' => NULL])
              ->addIndex('user_id',['name' => 'user_id'])
              ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_users_cards_user_id'])
              ->create();
    }
}
