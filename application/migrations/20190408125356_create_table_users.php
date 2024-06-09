<?php


use Phinx\Migration\AbstractMigration;

class CreateTableUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('ip_address', 'string',['limit' => 45])
              ->addColumn('username', 'string',['limit' => 100,'null' => TRUE, 'default' => NULL])
              ->addColumn('password', 'string',['limit' => 255])
              ->addColumn('email', 'string',['limit' => 255])
              ->addColumn('activation_selector', 'string',['limit' => 255,'null' => TRUE])
              ->addColumn('activation_code', 'string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('forgotten_password_selector', 'string',['limit' => 255,'null' => TRUE])
              ->addColumn('forgotten_password_code', 'string',['limit' => 255,'null' => TRUE,'default' => NULL])
              ->addColumn('forgotten_password_time', 'integer',['signed' => FALSE,'null' => TRUE,'default' => NULL])
              ->addColumn('remember_selector', 'string',['limit' => 255,'null' => TRUE])
              ->addColumn('remember_code', 'string',['limit' => 255,'signed' => FALSE,'null' => TRUE,'default' => NULL])
              ->addColumn('created_on', 'integer',['signed' => FALSE])
              ->addColumn('last_login', 'integer',['signed' => FALSE,'null' => TRUE,'default' => NULL])
              ->addColumn('active', 'boolean',['signed' => FALSE,'null' => TRUE,'default' => NULL])
              ->addColumn('first_name', 'string',['limit' => 50,'null' => TRUE,'default' => NULL])
              ->addColumn('last_name', 'string',['limit' => 50,'null' => TRUE,'default' => NULL])
              ->addColumn('company', 'string',['limit' => 100,'null' => TRUE,'default' => NULL])
              ->addColumn('phone', 'string',['limit' => 20,'null' => TRUE,'default' => NULL])
              ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex('email',[
                  'unique' => TRUE,
                  'name' => 'email'
              ])
              ->addIndex('activation_selector',[
                'unique' => TRUE,
                'name' => 'activation_selector'
            ])
            ->addIndex('forgotten_password_selector',[
                'unique' => TRUE,
                'name' => 'forgotten_password_selector'
            ])
            ->addIndex('remember_selector',[
                'unique' => TRUE,
                'name' => 'remember_selector'
            ])
              ->create();
    }
}
