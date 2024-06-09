<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableGymJobsRequirementsItems extends AbstractMigration
{
    public function change()
    {
        $tableGymEquipment = $this->table('gym_jobs_requirements_items');
        $tableGymEquipment->addColumn('name', 'string')
              ->addColumn('type', 'string',['limit'=>32])
              ->create();
    }
}
