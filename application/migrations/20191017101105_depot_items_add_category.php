<?php

use Phinx\Migration\AbstractMigration;

class DepotItemsAddCategory extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depot_items');
        $table->addColumn('category', 'integer', ['after' => 'name', 'default' => null, 'null' => true])->save();
    }
}
