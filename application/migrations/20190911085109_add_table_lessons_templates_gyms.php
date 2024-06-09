<?php

use Phinx\Migration\AbstractMigration;

class AddTableLessonsTemplatesGyms extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_templates_gyms');
        $table->addColumn('template_id', 'integer')
              ->addColumn('gym_id', 'string')

              ->addIndex('gym_id',['name' => 'gym_id'])
              ->addIndex('template_id',['name' => 'template_id'])
              ->addForeignKey('template_id', 'lessons_templates', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_templates_gyms_template_id'])
              ->create();
    }
}
