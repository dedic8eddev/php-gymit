<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableContracts extends AbstractMigration
{
    public function change()
    {
        $this->table('gym_contract_numbers')->drop()->save();
        $tbl = $this->table('contracts');
        $tbl->addColumn('client_id', 'integer')
            ->addColumn('created_by','integer')

            ->addColumn('contract_number', 'string',['limit' => 255])
            ->addColumn('membership_price_id','integer')
            ->addColumn('status','smallinteger', ['default'=>1, 'comment' => '1-active, 2-canceled'])
            ->addColumn('data', 'text', ['limit' => MysqlAdapter::TEXT_LONG])

            ->addColumn('date_created','timestamp',['default' => 'CURRENT_TIMESTAMP'])

            ->addIndex('client_id',['name' => 'client_id'])
            ->addIndex('created_by',['name' => 'created_by'])
            ->addIndex('membership_price_id',['name' => 'membership_price_id'])
            ->addForeignKey('client_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_contracts_client_id'])
            ->addForeignKey('created_by', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_contracts_admin_id'])
            ->addForeignKey('membership_price_id', 'membership_prices', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_contracts_membership_price_id'])

            ->create();
    }
}
