<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableEetCheckoutsLog extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('eet_checkouts_log');
        $tbl->addColumn('checkout_id','integer')
            ->addColumn('user_id','integer')       
            ->addColumn('state', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0,'comment'=>'0-close, 1-open'])   
            ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('amount','integer')
            ->addColumn('note','text', ['limit' => MysqlAdapter::TEXT_TINY, 'null' => TRUE, 'default' => NULL])
            
            ->addIndex('checkout_id',['name' => 'checkout_id'])            
            ->addForeignKey('checkout_id', 'eet_checkouts', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_eet_checkouts_log_checkout_id'])
            ->addIndex('user_id',['name' => 'user_id'])            
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_eet_checkouts_log_user_id'])


            ->create();
    }
}
