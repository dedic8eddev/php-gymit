<?php

use Phinx\Migration\AbstractMigration;

class EditTablePriceListAddAccountNumber extends AbstractMigration
{
    public function change()
    {
        $this
            ->table('price_list')
            ->addColumn('account_number', 'integer') // Autocont account num via $config["app"]["autocont_accounts"];
            ->update()
        ;
    }
}
