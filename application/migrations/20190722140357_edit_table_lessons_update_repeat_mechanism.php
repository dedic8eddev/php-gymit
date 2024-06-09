<?php

use Phinx\Migration\AbstractMigration;

class EditTableLessonsUpdateRepeatMechanism extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons');
        $table->removeColumn('repeating_days')
              ->addColumn('parent_lesson', 'integer', ['default' => null, 'null' => true])
              ->addIndex('parent_lesson',['name' => 'parent_lesson'])
              ->update();
    }
}
