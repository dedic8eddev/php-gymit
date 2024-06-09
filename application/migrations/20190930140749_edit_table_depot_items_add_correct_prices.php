<?php

use Phinx\Migration\AbstractMigration;

class EditTableDepotItemsAddCorrectPrices extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depot_items');
        $table->removeColumn('price')
              ->removeColumn('stock')
              ->addColumn('buy_price','decimal',['precision' => 10,'scale' => 2, 'default' => 0])
              ->addColumn('sale_price','decimal',['precision' => 10,'scale' => 2, 'default' => 0])
              ->addColumn('vat_value','decimal',['precision' => 10,'scale' => 2, 'default' => 0.21])
              ->save();
    }
}
