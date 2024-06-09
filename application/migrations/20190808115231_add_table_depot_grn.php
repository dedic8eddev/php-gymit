<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableDepotGrn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depot_grn');
        $table->addColumn('item_id','integer')
              ->addColumn('name','string',['limit' => 255,'null' => TRUE, 'default' => NULL])
              ->addColumn('quantity','integer')
              ->addColumn('income_type','integer',['limit' => MysqlAdapter::INT_TINY,'signed' => FALSE,'comment' => '1. Nové zboží, 2. Příjem/Naskladnění zboží'])
              ->addColumn('warehouseman_user','integer')
              ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex('item_id',['name' => 'item_id'])
              ->addIndex('warehouseman_user',['name' => 'warehouseman_user'])
              ->addForeignKey('item_id', 'depot_items', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_depot_grn_item_id'])
              ->addForeignKey('warehouseman_user', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_depot_grn_warehouseman_user'])
              ->create();
    }
}
