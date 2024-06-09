<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableBlogAddColPin extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('blog');
        $table->addColumn('pin', 'integer', ['limit' => 4, 'null' => TRUE, 'default' => NULL, 'after' => 'state'])
            ->update();
    }
}
