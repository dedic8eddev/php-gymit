<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableCoachSpecializations extends AbstractMigration
{
    public function change()
    {
        $tableSpec = $this->table('coach_specializations');
        $tableSpec->addColumn('coach_id', 'integer')
              ->addColumn('specialization_id', 'integer')

              ->addIndex('coach_id',['name' => 'coach_id'])
              ->addIndex('specialization_id',['name' => 'specialization_id'])
              ->addForeignKey('coach_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_coaches_specializations_coach_id'])
              ->addForeignKey('specialization_id', 'coach_specializations_items', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_coach_specializations_specialization_id'])
              
              ->create();
    }
}
