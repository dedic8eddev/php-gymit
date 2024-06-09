<?php


use Phinx\Seed\AbstractSeed;

class FakeClientsSeeder extends AbstractSeed
{
    /**
     * @return array
     */
    public function getDependencies()
    {
        return [
            'UserSeeder',
        ];
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
        $fakeClient = [
            [
                'ip_address'              => '127.0.0.1',
                'username'                => 'fake_client_1',
                'password'                => '$2y$10$N6jxLbUEvDgEFGfHDXbq5enzPBhKP6HHUy9mKbzp4bc2kzUv22XIu',
                'email'                   => 'fake1@admin.com',
                'activation_code'         => '',
                'forgotten_password_code' => NULL,
                'date_created'            => date('Y-m-d H:i:s'),
                'last_login'              => '1268889823',
                'active'                  => '1',
                'created_by'              => '0'
            ]
        ];
        $this->query('SET FOREIGN_KEY_CHECKS=0');
        $users = $this->table('users');
        $users->insert($fakeClient)->save();


        // admin user data
        $userData = [
            'user_id' => $this->getAdapter()->getConnection()->lastInsertId(),
            'email' => 'fake1@admin.com',
            'first_name' => 'Bez karty',
            'last_name' => '00_Prodej',
            'gdpr' => 1,
        ];

        // user groups data
        $userGroupsData = [
            'user_id' => $this->getAdapter()->getConnection()->lastInsertId(),
            'group_id' => CLIENT,
        ];


        $usersData = $this->table('users_data');
        $usersData->insert($userData)->save();
        $usersGroups = $this->table('users_groups');
        $usersGroups->insert($userGroupsData)->save();
        $this->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
