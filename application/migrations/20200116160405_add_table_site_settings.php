<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableSiteSettings extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('site_settings');
        $tbl->addColumn('gym', 'text', ['limit' => MysqlAdapter::TEXT_TINY])
            ->addColumn('current_site','text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true, 'default' => null])
            ->create();
    }
}
