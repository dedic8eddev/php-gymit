<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableVouchersAddIdentification extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('vouchers');
        $tbl->addColumn('identification_type', 'string',['null' => TRUE,'default' => NULL, 'after' => 'code'])
            ->addColumn('identification_id', 'string',['null' => TRUE,'default' => NULL, 'after' => 'identification_type'])
            
            ->addIndex('identification_type',['name' => 'identification_type'])
            ->addIndex('identification_id',['name' => 'identification_id'])
            ->addIndex('code',['name' => 'code'])
            ->update();
    }
}
