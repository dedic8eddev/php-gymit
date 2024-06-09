<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableGpwebpayLog extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('gpwebpay_log');
        $tbl->addColumn('webpay_id', 'biginteger', ['limit' => 32])
            ->addColumn('gym_code', 'string', ['limit' => 10])
            ->addColumn('client_id', 'integer')
            ->addColumn('paid', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('prcode', 'integer',['null' => TRUE])
            ->addColumn('srcode', 'integer',['null' => TRUE])
            ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])

            ->addIndex('webpay_id', ['name' => 'webpay_id', 'unique' => TRUE])
            ->addIndex('client_id',['name' => 'client_id'])
            ->addForeignKey('client_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_gpwebpay_log_client_id'])            
          
            ->create();
    }
}
