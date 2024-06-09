<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddReaderProcessingTable extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('reader_processing');
        $tbl->addColumn('last_event_created_on', 'string',['limit' => 255])
            ->addColumn('date_processed','timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
