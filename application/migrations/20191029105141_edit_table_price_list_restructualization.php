<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTablePriceListRestructualization extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('price_list');
        $tbl->removeColumn('hp_position')
            ->removeColumn('period_type')
            ->removeColumn('period_value')
            ->addColumn('service_type', 'integer', ['limit' => 4, 'null' => TRUE, 'default' => NULL, 'after' => 'id'])
            ->addColumn('vat', 'decimal', ['precision' => 10,'scale' => 2, 'default' => 0.21, 'after' => 'price', 'comment' => 'VAT value'])
            ->addColumn('vat_price', 'decimal', ['precision' => 10,'scale' => 2, 'after' => 'vat', 'comment' => 'Price with VAT'])
            ->changeColumn('price', 'decimal', ['precision' => 10,'scale' => 2])
            ->changeColumn('name', 'string', ['null' => TRUE, 'default' => NULL])
            ->changeColumn('description', 'text', ['null' => TRUE, 'default' => NULL, 'comment' => 'popis ceny'])
            ->update();
    }
}