<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class UpdateDepotGdn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depot_gdn');
        $table->addColumn('reason', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'after' => 'quantity'])
              ->update();
    }
}
