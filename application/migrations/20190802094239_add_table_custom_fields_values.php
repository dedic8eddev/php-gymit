<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableCustomFieldsValues extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('custom_fields_values');
        $table->addColumn('field_id', 'integer')
              ->addColumn('item_id', 'string',['limit' => 255])
              ->addColumn('section','string')
              ->addColumn('value','text',['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])

              ->addIndex('field_id',['name' => 'field_id'])
              ->addForeignKey('field_id', 'custom_fields', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_custom_fields_values_field_id'])

              ->create();
    }
}
