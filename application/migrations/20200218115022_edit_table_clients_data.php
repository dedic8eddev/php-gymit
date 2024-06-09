<?php

use Phinx\Migration\AbstractMigration;

class EditTableClientsData extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('clients_data');
        $tbl->addColumn('membership_id', 'integer', ['length' => 11, 'null' => TRUE, 'default' => NULL])
            ->addIndex('membership_id',['name' => 'membership_id'])
            ->addForeignKey('membership_id', 'membership', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_clients_data_membership_id'])
            ->update();
    }
}
