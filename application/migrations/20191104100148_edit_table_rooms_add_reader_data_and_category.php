<?php

use Phinx\Migration\AbstractMigration;

class EditTableRoomsAddReaderDataAndCategory extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('rooms');
        $tbl->addColumn('reader_sn', 'string', ['null' => TRUE, 'default' => NULL])
            ->addColumn('category', 'integer', ['null' => TRUE, 'default' => NULL])
            ->update();
    }
}
