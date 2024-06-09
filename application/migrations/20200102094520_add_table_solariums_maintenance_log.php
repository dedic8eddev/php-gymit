<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableSolariumsMaintenanceLog extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('solariums_maintenance_log');
        $tbl->addColumn('solarium_id', 'integer')    
            ->addColumn('change_pipes', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('note','string', ['null' => true, 'default' => NULL])
            ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by','integer')

            ->addIndex('solarium_id',['name' => 'solarium_id'])
            ->addForeignKey('solarium_id', 'solariums', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_solariums_maintenance_log_solarium_id'])            
            ->addIndex('created_by',['name' => 'created_by'])
            ->addForeignKey('created_by', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_solariums_maintenance_log_created_by'])

            ->create();
    }
}
