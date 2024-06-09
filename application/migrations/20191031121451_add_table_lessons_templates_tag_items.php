<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableLessonsTemplatesTagItems extends AbstractMigration
{
    public function change()
    {
        $tableTagItems = $this->table('lessons_templates_tag_items');
        $tableTagItems->addColumn('name', 'string')
              ->create();
    }
}
