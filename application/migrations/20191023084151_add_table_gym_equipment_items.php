<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableGymEquipmentItems extends AbstractMigration
{
    public function change()
    {
        $tableGymEquipment = $this->table('gym_equipment_items');
        $tableGymEquipment->addColumn('name', 'string')
              ->create();
    }
}
