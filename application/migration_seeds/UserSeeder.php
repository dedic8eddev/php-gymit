<?php


use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
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
        $adminUserData = [
            [
                'ip_address'              => '127.0.0.1',
                'username'                => 'administrator',
                'password'                => '$2y$10$N6jxLbUEvDgEFGfHDXbq5enzPBhKP6HHUy9mKbzp4bc2kzUv22XIu',
                'email'                   => 'admin@admin.com',
                'activation_code'         => '',
                'forgotten_password_code' => NULL,
                'date_created'            => '2019-01-01 00:00:00',
                'last_login'              => '1268889823',
                'active'                  => '1'
            ]
        ];

        // user groups data
        $userGroupsData = [
            [
                'id' => 1,
                'user_id' => 1,
                'group_id' => ADMINISTRATOR,
            ]
        ];

        // admin user data
        $userData = [
            'user_id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@admin.com',
            'gdpr' => 1,
        ];

        $users = $this->table('users');
        $usersGroups = $this->table('users_groups');
        $users->insert($adminUserData)->save();
        $usersGroups->insert($userGroupsData)->save();
        $usersData = $this->table('users_data');
        $usersData->insert($userData)->save();
    }
}
