<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableGpwebpayCodes extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('gpwebpay_codes');
        $tbl->addColumn('type', 'string',['limit' => 255, 'comment' => 'PRCODE, SRCODE'])
            ->addColumn('code', 'integer')
            ->addColumn('text', 'text')
            ->addColumn('text_en', 'text')            
            ->create();
    }
}
