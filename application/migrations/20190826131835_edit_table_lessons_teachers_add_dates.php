<?php

use Phinx\Migration\AbstractMigration;

class EditTableLessonsTeachersAddDates extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_teachers');
        $table->addColumn('lesson_date', 'date', ['null' => true, 'default' => null])
              ->update();
    }
}
