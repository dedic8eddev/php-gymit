<?php

use Phinx\Migration\AbstractMigration;

class EditTableDepotItemsAddDepotId extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depots_stocks');
        $table->addColumn('item_id', 'integer')
              ->addColumn('depot_id', 'integer')
              ->addColumn('stock', 'integer')

              ->addIndex('depot_id',['name' => 'depot_id'])
              ->addForeignKey('depot_id', 'depots', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_depot_stocks_depot_id'])
              ->addIndex('item_id',['name' => 'item_id'])
              ->addForeignKey('item_id', 'depot_items', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_depot_stocks_item_id'])
              ->create();
    }
}
