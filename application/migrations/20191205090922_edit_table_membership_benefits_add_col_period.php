<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableMembershipBenefitsAddColPeriod extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_benefits');
        $tbl->addColumn('period_type','string',['limit'=>32, 'null' => TRUE, 'default' => NULL, 'comment' => 'day,week,month,year', 'after' => 'discount'])
            ->addColumn('period_value','integer',['limit' => MysqlAdapter::INT_TINY,'null' => TRUE, 'default' => NULL, 'after' => 'period_type'])
            ->update();
    }
}
