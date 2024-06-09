<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableVouchersAddColType extends AbstractMigration
{
    public function change()
    {
        $this->query('SET FOREIGN_KEY_CHECKS=0');
        $tbl = $this->table('vouchers');
        $tbl->changeColumn('price_id', 'integer',['null' => TRUE,'default' => NULL])
            ->addColumn('membership_price_id', 'integer',['null' => TRUE,'default' => NULL, 'after' => 'price_id'])

            ->addIndex('membership_price_id',['name' => 'membership_price_id'])
            ->addForeignKey('membership_price_id', 'membership_prices', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_vouchers_membership_price_id'])
            ->update();
        $this->query('SET FOREIGN_KEY_CHECKS=1');

    }
}
