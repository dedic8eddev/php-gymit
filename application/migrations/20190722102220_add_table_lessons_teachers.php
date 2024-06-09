<?php

use Phinx\Migration\AbstractMigration;

class AddTableLessonsTeachers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_teachers');
        $table->addColumn('lesson_id', 'integer')
              ->addColumn('teacher_id', 'integer')

              ->addIndex('lesson_id',['name' => 'lesson_id'])
              ->addForeignKey('lesson_id', 'lessons', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_teachers_lessonid'])
              ->addIndex('teacher_id',['name' => 'teacher_id'])
              ->addForeignKey('teacher_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_teachers_teacherid'])

              ->create();
    }
}
