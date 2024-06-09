<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableMembershipServicesPrices extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_services_prices');
        $tbl->addColumn('price_id','integer')
            ->addColumn('membership_id','integer')

            ->addColumn('price', 'decimal', ['precision' => 10,'scale' => 2])
            ->addColumn('vat', 'decimal', ['precision' => 10,'scale' => 2, 'default' => 0.21, 'after' => 'price', 'comment' => 'VAT value'])
            ->addColumn('vat_price', 'decimal', ['precision' => 10,'scale' => 2, 'after' => 'vat', 'comment' => 'Price with VAT'])

            ->addIndex('price_id',['name' => 'price_id'])
            ->addIndex('membership_id',['name' => 'membership_id'])

            ->addForeignKey('price_id', 'price_list', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_memebrship_services_prices_price_id'])
            ->addForeignKey('membership_id', 'membership', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_membership_services_prices_membership_id'])
            
            ->create();
    }
}
