<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableLessonsClientsAddColFee extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('lessons_clients');
        $tbl->addColumn('reservation_fee_paid', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0,'after' => 'client_id'])
            ->addColumn('reservation_fee_refunded', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0,'after' => 'reservation_fee_paid'])
            ->addColumn('state','integer',['limit' => MysqlAdapter::INT_TINY,'null' => TRUE, 'default' => NULL, 'after' => 'reservation_fee_refunded', 'comment' => '1-came, 2-did not come, 3-canceled'])
            ->update();
    }
}
