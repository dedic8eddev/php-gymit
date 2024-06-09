<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableMembershipBenefitsAddColSpecificHours extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_benefits');
        $tbl->changeColumn('quantity', 'integer', ['null' => TRUE])
            ->removeColumn('period_value')
            ->addColumn('membership_id', 'integer', ['after' => 'id'])
            ->addColumn('specific_hour_start', 'time', ['null' => TRUE, 'after' => 'period_type'])
            ->addColumn('specific_hour_end', 'time', ['null' => TRUE, 'after' => 'specific_hour_start'])

            ->addIndex('membership_id',['name' => 'membership_id'])

            ->addForeignKey('membership_id', 'membership', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_membership_benefits_membership_id'])
            ->update();

    }
}
