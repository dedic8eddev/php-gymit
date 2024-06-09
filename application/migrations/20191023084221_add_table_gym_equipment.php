<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableGymEquipment extends AbstractMigration
{
    public function change()
    {
        $tableSpec = $this->table('gym_equipment');
        $tableSpec->addColumn('type', 'string')
              ->addColumn('equipment_id', 'integer')

              ->addIndex('equipment_id',['name' => 'equipment_id'])
              ->addForeignKey('equipment_id', 'gym_equipment_items', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_gym_equipment_equipment_id'])
              
              ->create();
    }
}
