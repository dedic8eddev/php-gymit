<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateTableAutocontExpectedPayments extends AbstractMigration
{
    public function change()
    {
        /** 
         * Table that holds simple rows with all expected payments to receive from Autocont
         * This is to simplify queries and allow for better overview
        */
        $tbl = $this->table('autocont_expected_payments');
        $tbl->addColumn('client_id', 'integer')

            ->addColumn('paid', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('cancelled', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('received', 'decimal', ['precision' => 10,'scale' => 2, 'default' => 0])

            ->addColumn('transactionNumber','text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('autocont_notified', 'boolean', ['default' => FALSE])
            ->addColumn('autocont_type', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => true, 'default' => null]) // Predplat / Kredit / Voucher

            ->addIndex('client_id',['name' => 'client_id'])
            ->addForeignKey('client_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_autocont_exp_payments_client_id'])            

            ->create();
    }
}
