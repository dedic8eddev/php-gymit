<?php

use Phinx\Migration\AbstractMigration;

class AddTableLessonsSkipDays extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_cancelled');
        $table->addColumn('lesson_id', 'integer')
              ->addColumn('lesson_date', 'date')

              ->addIndex('lesson_id',['name' => 'lesson_id'])
              ->addForeignKey('lesson_id', 'lessons', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_cancelled_lesson_id'])

              ->create();
    }
}
