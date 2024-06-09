<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableMembershipPricesVat extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_prices');
        $tbl->changeColumn('vat', 'decimal', ['precision' => 10,'scale' => 2, 'default' => 0.21, 'after' => 'price', 'comment' => 'VAT value'])
            ->update();

        $this->query('update membership_prices set vat=vat/100');
        
    }
}
