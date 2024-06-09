<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableMembershipPrices extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_prices');
        $tbl->addColumn('membership_id','integer',['null' => TRUE, 'default' => NULL])
            ->addColumn('period_type','string',['limit'=>32, 'null' => TRUE, 'default' => NULL, 'comment' => 'day,month,year'])
            ->addColumn('period_value','integer',['limit' => MysqlAdapter::INT_TINY,'null' => TRUE, 'default' => NULL])
            ->addColumn('vat', 'integer', ['limit' => 4, 'default' => 21, 'after' => 'price', 'comment' => 'VAT in percentage'])
            ->addColumn('price', 'decimal', ['precision' => 10,'scale' => 2])
            ->addForeignKey('membership_id', 'membership', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_membership_membership_id'])
            ->create();
    }
}
