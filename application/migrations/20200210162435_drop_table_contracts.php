<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class DropTableContracts extends AbstractMigration
{
    public function change()
    {
        $this->table('contracts')->drop()->save();
    }
}
