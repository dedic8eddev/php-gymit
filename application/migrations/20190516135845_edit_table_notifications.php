<?php


use Phinx\Migration\AbstractMigration;

class EditTableNotifications extends AbstractMigration
{
    public function change()
    {
        $notifications = $this->table('notifications');
        $notifications->addColumn('group','integer',['default' => NULL, 'null' => TRUE, 'after' => 'target'])
                      ->save();
    }
}
