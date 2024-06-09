<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableDepotInvoices extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('depot_invoices');
        $tbl->addColumn('created_by','integer')

            ->addColumn('invoice_number', 'string',['limit' => 255])
            ->addColumn('invoice_name', 'string',['limit' => 255])
            ->addColumn('items', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('note', 'text', ['limit' => MysqlAdapter::TEXT_LONG])

            ->addColumn('created_on','timestamp', ["default" => "CURRENT_TIMESTAMP"])

            ->addIndex('created_by',['name' => 'created_by'])
            ->addForeignKey('created_by', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_depot_invoices_admin_id'])
            
            ->create();
    }
}
