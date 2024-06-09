<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddColumnDefaultLocationGroups extends AbstractMigration
{
    public function change()
    {
        $this
            ->table('groups')
            ->addColumn('default_location', 'string', ['limit' => 200, 'null' => FALSE, 'default' => 'account'])
            ->update()
        ;
    }
}
