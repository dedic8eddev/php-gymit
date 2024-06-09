<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTablePriceListAddColIconImage extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('price_list');
        $tbl->addColumn('icon_image', 'integer', ['limit' => 4, 'default' => 1, 'after' => 'service_subtype'])
            ->update();
    }
}
