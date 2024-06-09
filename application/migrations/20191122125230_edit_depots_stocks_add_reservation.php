<?php

use Phinx\Migration\AbstractMigration;

class EditDepotsStocksAddReservation extends AbstractMigration
{

    public function change()
    {
        $tbl = $this->table('depots_stocks');
        $tbl->addColumn('reserved', 'integer', ['null' => false, 'default' => 0])
            ->update();
    }
}
