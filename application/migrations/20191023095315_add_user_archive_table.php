<?php

use Phinx\Migration\AbstractMigration;

class AddUserArchiveTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users_archive');
        $table->addColumn('original_id', 'integer')
              ->addColumn('email', 'string',['limit' => 255])
              ->addColumn('first_name', 'string',['limit' => 50,'null' => TRUE,'default' => NULL])
              ->addColumn('last_name', 'string',['limit' => 50,'null' => TRUE,'default' => NULL])
              ->addColumn('phone', 'string',['limit' => 20,'null' => TRUE,'default' => NULL])
              ->addColumn('street','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('city','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('zip','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('country','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('vat_enabled', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
              ->addColumn('vat_id','string',['limit' => 255, 'null' => TRUE,'default' => NULL]) //DIČ
              ->addColumn('company_id','string',['limit' => 255,'null' => TRUE,'default' => NULL]) //IČ
              ->addColumn('date_created', 'timestamp')
              ->addColumn('date_archived', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}
