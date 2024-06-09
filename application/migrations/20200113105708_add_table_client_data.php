<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableClientData extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('clients_data');
        $tbl->addColumn('client_id', 'integer')
            ->addColumn('multisport_id', 'integer', ['null' => TRUE, 'default' => NULL])
            ->addColumn('vip', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('dailypass', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('forbidden_access', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('forbidden_access_reason', 'text',['null'=>TRUE,'default'=>NULL])

            ->addIndex('client_id',['name' => 'client_id'])
            ->addForeignKey('client_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_clients_data_client_id'])
            
            ->create();

        $tbl = $this->table('users_data');
        $tbl->removeColumn('multisport_id')
            ->update();
    }
}