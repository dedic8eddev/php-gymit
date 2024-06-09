<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableBlogAddImage extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('blog');
        $table->addColumn('image', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'after' => 'text'])
              ->update();
    }
}
