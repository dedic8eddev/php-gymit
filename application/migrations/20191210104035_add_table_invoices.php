<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableInvoices extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('invoices');
        $tbl->addColumn('client_id', 'integer')
            ->addColumn('created_by','integer')

            ->addColumn('invoice_number', 'string',['limit' => 255])
            ->addColumn('items', 'text', ['limit' => MysqlAdapter::TEXT_LONG])

            ->addColumn('payment_method', 'integer')
            ->addColumn('client_name', 'string',['limit' => 255])
            ->addColumn('client_street', 'string',['limit' => 255])
            ->addColumn('client_city', 'string',['limit' => 255])
            ->addColumn('client_zip', 'string',['limit' => 255])
            ->addColumn('client_state', 'string',['limit' => 255])
            ->addColumn('client_company_id', 'string',['limit' => 255])
            ->addColumn('client_vat_id', 'string',['limit' => 255])

            ->addColumn('value', 'decimal',['precision' => 10,'scale' => 2, 'default' => 0])
            ->addColumn('vat_value', 'decimal',['precision' => 10,'scale' => 2, 'default' => 0])

            ->addColumn('date_created','timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('issue_date','date', ['null' => true, 'default' => NULL])
            ->addColumn('due_date','date', ['null' => true, 'default' => NULL])
            ->addColumn('payment_date','date', ['null' => true, 'default' => NULL])

            ->addColumn('paid','boolean', ['default' => false])
            ->addColumn('status','integer')
            ->addColumn('cancelled','boolean', ['default' => false])

            ->addIndex('client_id',['name' => 'client_id'])
            ->addIndex('created_by',['name' => 'created_by'])
            ->addForeignKey('client_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_invoices_client_id'])
            ->addForeignKey('created_by', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_invoices_admin_id'])
            
            ->create();
    }
}
