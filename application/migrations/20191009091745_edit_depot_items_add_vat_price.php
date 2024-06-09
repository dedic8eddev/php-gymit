<?php

use Phinx\Migration\AbstractMigration;

class EditDepotItemsAddVatPrice extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depot_items');
        $table->addColumn('sale_price_vat','decimal',['precision' => 10,'scale' => 2, 'default' => 0])
              ->save();
    }
}
