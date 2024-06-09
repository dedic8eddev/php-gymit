<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableLessons extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons');
        $table->addColumn('created_on', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('created_by', 'integer')
              ->addColumn('starting_on', 'timestamp', ['null' => true, 'default' => null])
              ->addColumn('name', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => TRUE, 'default' => NULL])
              ->addColumn('description', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => TRUE, 'default' => NULL])
              ->addColumn('repeating', 'integer', ['null' => true, 'default' => null])
              ->addColumn('all_day', 'boolean', ['default' => 1])
              ->addColumn('entry_fee', 'integer', ['default' => 0])

              ->addIndex('created_by',['name' => 'created_by'])
              ->addForeignKey('created_by', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_creator_id'])

              ->create();
    }
}
