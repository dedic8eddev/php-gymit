<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableUsersAddColCreatedBy extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('users');
        $tbl->addColumn('created_by', 'integer', ['null' => TRUE,'default' => NULL, 'after' => 'date_created'])
            ->addIndex('created_by',['name' => 'created_by'])
            ->addForeignKey('created_by', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_users_created_by'])
            ->update();
    }
}
