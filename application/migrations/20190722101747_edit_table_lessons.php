<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableLessons extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons');
        $table->addColumn('photo', 'text', ['null' => true, 'default' => null])
              ->addColumn('gym_id', 'integer', ['null' => true, 'default' => null])
              ->addColumn('repeating_start', 'date', ['null' => true, 'default' => null, 'after' => 'repeating'])
              ->addColumn('repeating_end', 'date', ['null' => true, 'default' => null, 'after' => 'repeating_start'])
              ->addColumn('repeating_days', 'text', ['null' => true, 'default' => null, 'after' => 'repeating'])
              ->addColumn('ending_on', 'timestamp', ['after' => 'starting_on', 'default' => null, 'null' => true])
              ->update();
    }
}
