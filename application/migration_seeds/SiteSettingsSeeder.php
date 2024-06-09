<?php


use Phinx\Seed\AbstractSeed;

class SiteSettingsSeeder extends AbstractSeed
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
        // solariums data
        $settingsDefault = [
            [
                'id'                => 1,
                'gym'               => '01',
                'current_site'      => NULL,
            ]                               
        ];

        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 0');
        $truncate = $this->query('truncate site_settings');
        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 1');
        $solariums = $this->table('site_settings');
        $solariums->insert($settingsDefault)->save();
    }
}
