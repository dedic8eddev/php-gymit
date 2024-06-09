<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableMembershipTypes extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_types');
        $tbl->addColumn('code','string',['limit' => 255])
            ->addColumn('name','string',['limit' => 255])
            ->create();

        $table = $this->table('membership');
        $this->query('SET FOREIGN_KEY_CHECKS=0');
        $table->addColumn('type_id', 'integer', ['after' => 'id'])
            ->addIndex('type_id',['name' => 'type_id'])
            ->addForeignKey('type_id', 'membership_types', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_memebrship_type_id'])
            ->update(); 
        $this->query('SET FOREIGN_KEY_CHECKS=1');           
    }
}
