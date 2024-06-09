<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableVouchers extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('vouchers');
        $tbl->addColumn('price_id', 'integer')
            ->addColumn('code', 'string',['limit' => 255])
            ->addColumn('note','string', ['null' => true, 'default' => NULL])
            ->addColumn('gifted_user','integer', ['null' => true, 'default' => NULL])
            ->addColumn('date_disabled','timestamp', ['null' => true, 'default' => NULL])
            ->addColumn('disabled_by','integer', ['null' => true, 'default' => NULL])
            ->addColumn('date_created','timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by','integer')

            ->addIndex('price_id',['name' => 'price_id'])
            ->addIndex('gifted_user',['name' => 'gifted_user'])
            ->addIndex('disabled_by',['name' => 'disabled_by'])
            ->addIndex('created_by',['name' => 'created_by'])

            ->addForeignKey('price_id', 'price_list', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_vouchers_price_id'])
            ->addForeignKey('gifted_user', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_vouchers_gifted_user'])
            ->addForeignKey('disabled_by', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_vouchers_disabled_by'])
            ->addForeignKey('created_by', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_vouchers_created_by'])
            
            ->create();
    }
}
