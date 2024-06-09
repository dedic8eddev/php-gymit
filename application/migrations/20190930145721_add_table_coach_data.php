<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableCoachData extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('coach_data');
        $table->addColumn('coach_id', 'integer')
              ->addColumn('about','text',['limit' => MysqlAdapter::TEXT_LONG])

              ->addIndex('coach_id',['name' => 'coach_id'])
              ->addForeignKey('coach_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_coaches_data_coach_id'])
              
              ->create();
    }
}
