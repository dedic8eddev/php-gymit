<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableLessonsTemplatesTags extends AbstractMigration
{
    public function change()
    {
        $tblTags = $this->table('lessons_templates_tags');
        $tblTags->addColumn('lesson_id', 'integer')
                ->addColumn('tag_id', 'integer')

                ->addIndex('lesson_id',['name' => 'lesson_id'])
                ->addIndex('tag_id',['name' => 'tag_id'])
                ->addForeignKey('lesson_id', 'lessons_templates', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_templates_tags_lesson_id'])
                ->addForeignKey('tag_id', 'lessons_templates_tag_items', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_templates_tags_tag_id'])

                ->create();
    }
}
