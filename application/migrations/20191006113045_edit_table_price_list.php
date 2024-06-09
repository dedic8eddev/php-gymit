<?php


use Phinx\Migration\AbstractMigration;

class EditTablePriceList extends AbstractMigration
{
    public function change()
    {
        $pricelist = $this->table('price_list');
        $pricelist->addColumn('hp_position','integer',['default' => NULL, 'null' => TRUE])
                      ->save();
    }
}
