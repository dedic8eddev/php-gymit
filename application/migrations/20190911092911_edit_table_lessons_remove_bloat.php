<?php

use Phinx\Migration\AbstractMigration;

class EditTableLessonsRemoveBloat extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons');
        $table->removeColumn('name')
              ->removeColumn('description')
              ->removeColumn('photo')

              ->addColumn('template_id', 'integer')
              ->addIndex('template_id',['name' => 'template_id'])
              ->addForeignKey('template_id', 'lessons_templates', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_template_id'])
              ->save();
    }
}
