<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableLessonsTemplates extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_templates');
        $table->addColumn('name', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
              ->addColumn('description', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => TRUE, 'default' => NULL])
              ->addColumn('text','text', ['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])
              ->addColumn('photo', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true, 'default' => null])
              ->addColumn('base_price', 'integer', ['null' => TRUE, 'default' => NULL])
              ->create();
    }
}
