<?php


use Phinx\Seed\AbstractSeed;

class GroupSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        // groups data
        $groupsData = [
            [
                'id'                => ADMINISTRATOR,
                'name'              => 'admin',
                'description'       => 'Administrátor',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => STORE_MANAGER,
                'name'              => 'storeManager',
                'description'       => 'Manažer provozovny',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => SENIOR_RECEPTIONIST,
                'name'              => 'seniorReceptionist',
                'description'       => 'Senior recepční',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => RECEPTIONIST,
                'name'              => 'receptionist',
                'description'       => 'Recepční',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => WELLNESS_SERVICE,
                'name'              => 'wellnessService',
                'description'       => 'Obsluha wellness',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => CHILDREN_PLAY_AREA_WORKER,
                'name'              => 'childrenPlayAreaWorker',
                'description'       => 'Pracovník dětského koutku',
                'default_location'  => 'admin/lessons',
            ],
            [
                'id'                => WEBMASTER,
                'name'              => 'webmaster',
                'description'       => 'Správce webu',
                'default_location'  => 'admin/lessons',
            ],
            [
                'id'                => GYM_AND_STUDIO_MANAGER,
                'name'              => 'gymAndStudioManager',
                'description'       => 'Gym and studio manager',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => MASTER_TRAINER,
                'name'              => 'masterTrainer',
                'description'       => 'Master trainer',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => PERSONAL_TRAINER,
                'name'              => 'personalTrainer',
                'description'       => 'Osobní trenér',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => INSTRUCTOR,
                'name'              => 'instructor',
                'description'       => 'Instruktor',
                'default_location'  => 'admin/lessons',
            ],
            [
                'id'                => SERVICE_TECHNICIAN,
                'name'              => 'serviceTechnican',
                'description'       => 'Servisní technik',
                'default_location'  => 'admin/dashboard',
            ],
            [
                'id'                => CLIENT,
                'name'              => 'client',
                'description'       => 'Zákazník',
                'default_location'  => 'account',
            ],
            [
                'id'                => DISPOSABLE,
                'name'              => 'disposable',
                'description'       => 'Jednorázový uživatel',
                'default_location'  => 'account',
            ],
        ];

        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 0');
        $truncate = $this->query('truncate groups');
        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 1');
        $groups = $this->table('groups');
        $groups->insert($groupsData)->save();
    }
}
