<?php


use Phinx\Seed\AbstractSeed;

class PricelistSeeder extends AbstractSeed
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


        // membership types
        $pricelistData = [
            // FIXED IDs IN CODE - DO NOT CHANGE ID!!
            [ 'id' => 1, 'service_type' => 10,'service_subtype' => 2, 
                'name' => 'Členská karta', 
                'price' => (300/1.21), 'vat_price' => 300, 
                'locked' => 1,
                'account_number' => 4
            ],
            [ 'id' => 2,'service_type' => 7, 'service_subtype' => 2, 
                'name' => 'Půjčení prostěradla', 
                'price' => (30/1.21), 'vat_price' => 30, 
                'locked' => 1,
                'account_number' => 6
            ],
            // END FIXED IDs                 
            [ 'id' => 3, 'service_type' => 1, 'service_subtype' => 1, 
                'name' => 'Jednorázový vstup (fitness)', 
                'price' => (250/1.21), 'vat_price' => 250, 
                'duration'=>'01:30:00', 'overtime_fee_minutes'=>15, 'overtime_fee_price'=>15, 
                'locked' => 1,
                'account_number' => 3
            ], 
            [ 'id' => 4, 'service_type' => 4, 'service_subtype' => 1, 
                'name' => 'Jednorázový vstup (wellness)', 
                'price' => (250/1.21), 'vat_price' => 250, 
                'duration'=>'01:30:00', 'overtime_fee_minutes'=>15, 'overtime_fee_price'=>15,  
                'locked' => 1,
                'account_number' => 3
            ],  
            [ 'id' => 5, 'service_type' => 3, 'service_subtype' => 1, 
                'name' => 'Skupinová lekce 30 minut', 
                'price' => (150/1.21), 'vat_price' => 150, 
                'duration'=>'00:30:00', 
                'locked' => 1,
                'account_number' => 6
            ],  
            [ 'id' => 6, 'service_type' => 3, 'service_subtype' => 1, 
                'name' => 'Skupinová lekce 60 minut', 
                'price' => (200/1.21), 'vat_price' => 200, 
                'duration'=>'01:00:00', 
                'locked' => 1,
                'account_number' => 6
            ],  
            [ 'id' => 7, 'service_type' => 3, 'service_subtype' => 1, 
                'name' => 'Skupinová lekce 90 minut', 
                'price' => (250/1.21), 'vat_price' => 250, 
                'duration'=>'01:30:00', 
                'locked' => 1,
                'account_number' => 6
            ],                                                                                                      
            [ 'id' => 8, 'service_type' => 5, 
                'name' => 'Solárium', 
                'price' => (20/1.21), 'vat_price' => 20, 
                'locked' => 1,
                'account_number' => 6
            ], 
            [ 'id' => 9, 'service_type' => 6, 
                'name' => 'Voucher (500,-)', 
                'price' => (500/1.21), 'vat_price' => 500, 
                'locked' => 1,
                'account_number' => 6
            ], 
            [ 'id' => 10, 'service_type' => 6, 
                'name' => 'Voucher (1000,-)', 
                'price' => (1000/1.21), 'vat_price' => 1000, 
                'locked' => 1,
                'account_number' => 6
            ], 
            [ 'id' => 11,'service_type' => 6, 
                'name' => 'Voucher (2000,-)', 
                'price' => (2000/1.21), 'vat_price' => 2000, 
                'locked' => 1,
                'account_number' => 6
            ],          
        ];

        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 0');
        $truncatePricelist = $this->query('truncate price_list');
        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 1');
        $pricelist = $this->table('price_list');
        $pricelist->insert($pricelistData)->save();
    }
}
