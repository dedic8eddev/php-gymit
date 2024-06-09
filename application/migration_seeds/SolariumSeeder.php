<?php


use Phinx\Seed\AbstractSeed;

class SolariumSeeder extends AbstractSeed
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
        $solariumsData = [
            [
                'id'                    => 1,
                'name'                  => 'Solarium 1',
                'usage_minutes_limit'  => 1200*60,
            ],
            [
                'id'                    => 2,
                'name'                  => 'Solarium 2',
                'usage_minutes_limit'  => 1000*60,
            ],                                     
        ];

        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 0');
        $truncate = $this->query('truncate solariums');
        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 1');
        $solariums = $this->table('solariums');
        $solariums->insert($solariumsData)->save();
    }
}
