<?php

use Phinx\Migration\AbstractMigration;

class AddTableLessonsTemplatesTeachers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_templates_teachers');
        $table->addColumn('template_id', 'integer')
              ->addColumn('teacher_id', 'integer')

              ->addIndex('template_id',['name' => 'template_id'])
              ->addForeignKey('template_id', 'lessons_templates', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_templates_teachers_template_id'])
              ->addIndex('teacher_id',['name' => 'teacher_id'])
              ->addForeignKey('teacher_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_templates_teachers_teacher_id'])

              ->create();
    }
}
