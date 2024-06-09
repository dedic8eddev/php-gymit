<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableUsersData extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users_data');
        $table->addColumn('user_id', 'integer')
              ->addColumn('first_name','string',['limit' => 255])
              ->addColumn('last_name','string',['limit' => 255])
              ->addColumn('identification','string',['limit' => 255, 'null' => true, 'default' => null])
              ->addColumn('identification_type','integer', ['null' => true, 'default' => null])
              ->addColumn('photo','text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => TRUE, 'default' => NULL])
              ->addColumn('birth_date', 'date', ['null' => true, 'default' => null])
              ->addColumn('email','string',['limit' => 255])
              ->addColumn('phone','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('street','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('city','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('zip','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('country','string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('gdpr', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
              ->addColumn('vat_enabled', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
              ->addColumn('vat_id','string',['limit' => 255, 'null' => TRUE,'default' => NULL]) //DIČ
              ->addColumn('company_id','string',['limit' => 255,'null' => TRUE,'default' => NULL]) //IČ
              ->addColumn('internal_note','text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => TRUE, 'default' => NULL])
              ->addIndex('user_id',['name' => 'user_id'])
              ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_user_data_user_id'])
              ->create();
    }
}
