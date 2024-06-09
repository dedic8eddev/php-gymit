<?php

use Phinx\Migration\AbstractMigration;

class AddTableLessonsClients extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_clients');
        $table->addColumn('lesson_id', 'integer')
              ->addColumn('client_id', 'integer')

              ->addIndex('lesson_id',['name' => 'lesson_id'])
              ->addForeignKey('lesson_id', 'lessons', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_clients_lessonid'])
              ->addIndex('client_id',['name' => 'client_id'])
              ->addForeignKey('client_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_clients_clientid'])

              ->create();
    }
}
