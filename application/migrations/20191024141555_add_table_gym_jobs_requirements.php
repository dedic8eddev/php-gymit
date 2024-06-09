<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableGymJobsRequirements extends AbstractMigration
{
    public function change()
    {
        $tableSpec = $this->table('gym_jobs_requirements');
        $tableSpec->addColumn('job_id', 'integer')
              ->addColumn('requirement_id', 'integer')

              ->addIndex('job_id',['name' => 'job_id'])
              ->addIndex('requirement_id',['name' => 'requirement_id'])
              ->addForeignKey('job_id', 'gym_jobs', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_gym_jobs_job_id'])
              ->addForeignKey('requirement_id', 'gym_jobs_requirements_items', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_gym_requirement_requirement_id'])
              
              ->create();
    }
}
