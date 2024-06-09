<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableMembership extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership');
        $tbl->addColumn('code','string',['limit' => 255])
            ->addColumn('name','string',['limit' => 255])
            ->create();
    }
}
