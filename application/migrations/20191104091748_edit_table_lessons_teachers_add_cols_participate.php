<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableLessonsTeachersAddColsParticipate extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('lessons_teachers');
        $tbl->addColumn('participate', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 1])
            ->addColumn('reason','text', ['limit' => MysqlAdapter::TEXT_TINY, 'null' => TRUE, 'default' => NULL])
            ->addColumn('teacher_substitute', 'integer', ['null' => true])
            ->addIndex('teacher_substitute',['name' => 'teacher_substitute'])
            ->addForeignKey('teacher_substitute', 'users', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_teachers_teacher_substitute'])
            ->update();
    }
}
