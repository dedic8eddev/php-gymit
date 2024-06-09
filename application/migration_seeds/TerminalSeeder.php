<?php


use Phinx\Seed\AbstractSeed;

class TerminalSeeder extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                "terminal_name" => "Terminál #1",
                "microservice_ip" => "80.188.249.90:3005"
            ],
            [
                "terminal_name" => "Terminál #2",
                "microservice_ip" => "80.188.249.90:3006"
            ]
        ];

        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 0');
        $truncate = $this->query('truncate terminals');
        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 1');
        $tbl = $this->table('terminals');
        $tbl->insert($data)->save();
    }
}
