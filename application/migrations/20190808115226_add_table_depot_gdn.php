<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableDepotGdn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depot_gdn');
        $table->addColumn('item_id','integer')
              ->addColumn('name','string',['limit' => 255,'null' => TRUE, 'default' => NULL])
              ->addColumn('quantity','integer')
              ->addColumn('dispatch_type','integer',['limit' => MysqlAdapter::INT_TINY,'signed' => FALSE,'comment' => '1. Obecné', 'default' => 1])
              ->addColumn('warehouseman_user','integer')
              ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex('item_id',['name' => 'item_id'])
              ->addIndex('warehouseman_user',['name' => 'warehouseman_user'])
              ->addForeignKey('item_id', 'depot_items', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_depot_gdn_item_id'])
              ->addForeignKey('warehouseman_user', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_depot_gdn_warehouseman_user'])
              ->create();
    }
}
