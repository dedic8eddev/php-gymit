<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableLessonsTemplatesAddColsTextTitleClientLimit extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_templates');
        $table->addColumn('text_title', 'string', ['null' => TRUE, 'default' => NULL, 'after' => 'description'])
              ->addColumn('client_limit', 'integer', ['limit' => 4, 'null' => TRUE, 'default' => NULL, 'after' => 'photo'])
            ->update();
    }
}
