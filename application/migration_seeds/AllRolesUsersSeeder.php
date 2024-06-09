<?php


use Phinx\Seed\AbstractSeed;

class AllRolesUsersSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return ['GroupSeeder'];
    }

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
        // admin user data
        $groups = ['ADMINISTRATOR','STORE_MANAGER','SENIOR_RECEPTIONIST','RECEPTIONIST','WELLNESS_SERVICE','CHILDREN_PLAY_AREA_WORKER','WEBMASTER','GYM_AND_STUDIO_MANAGER','MASTER_TRAINER','PERSONAL_TRAINER','INSTRUCTOR','SERVICE_TECHNICIAN','CLIENT','DISPOSABLE'];
        $id=100;
        foreach ($groups as $g){
            $users[] = [
                'id' => $id,
                'ip_address'              => '127.0.0.1', 
                'username'                => $g, 
                'password'                => '$2y$10$N6jxLbUEvDgEFGfHDXbq5enzPBhKP6HHUy9mKbzp4bc2kzUv22XIu',
                'email'                   => strtolower($g).'@admin.com',
                'activation_code'         => '',
                'forgotten_password_code' => NULL,
                'date_created'            => '2019-01-01 00:00:00',
                'last_login'              => '1268889823',
                'active'                  => '1'
            ];

            // user groups data
            $usersGroupsData[] = [
                'id'            => $id,
                'user_id'       => $id,
                'group_id'      => constant($g),
            ];

                    // admin user data
            $usersData[] = [
                'user_id'       => $id,
                'first_name'    => $g,
                'last_name'     => $g,
                'email'         => strtolower($g).'@admin.com',
                'gdpr'          => 1,
            ];
            $id++;
        }

        $usersTable = $this->table('users');
        $usersTable->insert($users)->save();
        $usersGroupsTable = $this->table('users_groups');
        $usersGroupsTable->insert($usersGroupsData)->save();
        $usersDataTable = $this->table('users_data');
        $usersDataTable->insert($usersData)->save();
    }
}
