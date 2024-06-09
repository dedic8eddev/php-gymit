<?php


use Phinx\Seed\AbstractSeed;

class MembershipSeeder extends AbstractSeed
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
        $membershipTypesData = [
            [ 'id' => 1, 'code' => 'basic', 'name' => 'Basic členství' ],
            [ 'id' => 2, 'code' => 'platinum', 'name' => 'Platinum členství' ],
            [ 'id' => 3, 'code' => 'trial', 'name' => 'Na zkoušku' ],
            [ 'id' => 4, 'code' => 'prepaid', 'name' => 'Dobíjecí karta' ]                                  
        ];

        // membership
        $membershipData = [
            [ 'id' => 1, 'type_id' => 1, 'code' => 'basic_unlimited', 'name' => 'Basic Unlimited', 'data' => '{
                "overtimeFee_wellness":"1",
                "overtimeFee_exercise_zones":"0",
                "wellness_after_excercise_zones_price":"90"
            }' ],
            [ 'id' => 2, 'type_id' => 1, 'code' => 'basic_off_peak', 'name' => 'Basic Off peak', 'data' => '{
                "from":"08:00:00",
                "to":"16:00:00",
                "peak_behaviour_as_membership":"prepaid_card",
                "overtimeFee_wellness":"1",
                "overtimeFee_exercise_zones":"0",
                "wellness_after_excercise_zones_price":"90"
            }' ],
            [ 'id' => 3, 'type_id' => 1, 'code' => 'basic_quarter', 'name' => 'Basic Quarterly', 'data' => '{
                "overtimeFee_wellness":"1",
                "overtimeFee_exercise_zones":"0",
                "wellness_after_excercise_zones_price":"90"
            }' ],
            [ 'id' => 4, 'type_id' => 1, 'code' => 'basic_student', 'name' => 'Basic Student', 'data' => '{
                "overtimeFee_wellness":"1",
                "overtimeFee_exercise_zones":"0",
                "wellness_after_excercise_zones_price":"90"
            }' ],            
            [ 'id' => 5, 'type_id' => 2, 'code' => 'platinum', 'name' => 'Platinum', 'data' => '{
                "overtimeFee_wellness":"0",
                "overtimeFee_exercise_zones":"0",
            }' ],
            [ 'id' => 6, 'type_id' => 2, 'code' => 'platinum_off_peak', 'name' => 'Platinum Off peak', 'data' => '{
                "from":"08:00:00",
                "to":"16:00:00",
                "peak_behaviour_as_membership":"prepaid_card",
                "overtimeFee_wellness":"0",
                "overtimeFee_exercise_zones":"0",
            }' ],            
            [ 'id' => 7, 'type_id' => 2, 'code' => 'platinum_quarter', 'name' => 'Platinum Quarterly', 'data' => '{
                "overtimeFee_wellness":"0",
                "overtimeFee_exercise_zones":"0",
            }' ],
            [ 'id' => 8, 'type_id' => 2, 'code' => 'platinum_student', 'name' => 'Platinum student', 'data' => '{
                "overtimeFee_wellness":"0",
                "overtimeFee_exercise_zones":"0",
            }' ],
            [ 'id' => 9, 'type_id' => 3, 'code' => 'trial', 'name' => 'Trial', 'data' => '{
                "overtimeFee_wellness":"0",
                "overtimeFee_exercise_zones":"0",
            }' ],
            [ 'id' => 10, 'type_id' => 4, 'code' => 'prepaid_card', 'name' => 'Dobíjecí karta', 'data' => '{
                "overtimeFee_wellness":"1",
                "overtimeFee_exercise_zones":"1",
                "wellness_after_excercise_zones_price":"90"
            }' ],                                                                                                   
        ];

        $membershipPricesData = [
            [ 'id' => 1, 'membership_id' => 1, 'purchase_name' => 'Měsíční členství', 'period_type' => 'month', 'price' => 990 ],
            [ 'id' => 2, 'membership_id' => 1, 'purchase_name' => 'Roční členství', 'period_type' => 'year', 'price' => 9990 ],
            [ 'id' => 3, 'membership_id' => 2, 'purchase_name' => 'Měsíční členství', 'period_type' => 'month', 'price' => 690 ],
            [ 'id' => 4, 'membership_id' => 2, 'purchase_name' => 'Roční členství', 'period_type' => 'year', 'price' => 6990 ],
            [ 'id' => 5, 'membership_id' => 3, 'purchase_name' => 'Čtvrtletní členství', 'period_type' => 'quarter', 'price' => 3000 ],
            [ 'id' => 6, 'membership_id' => 4, 'purchase_name' => 'Měsíční členství', 'period_type' => 'month', 'price' => 790, ],
            [ 'id' => 7, 'membership_id' => 4, 'purchase_name' => 'Roční členství', 'period_type' => 'year', 'price' => 7990 ],
            [ 'id' => 8, 'membership_id' => 5, 'purchase_name' => 'Měsíční členství', 'period_type' => 'month', 'price' => 1290 ],
            [ 'id' => 9, 'membership_id' => 5, 'purchase_name' => 'Roční členství', 'period_type' => 'year', 'price' => 12990 ],
            [ 'id' => 10, 'membership_id' => 6, 'purchase_name' => 'Měsíční členství', 'period_type' => 'month', 'price' => 790 ],
            [ 'id' => 11, 'membership_id' => 6, 'purchase_name' => 'Roční členství', 'period_type' => 'year', 'price' => 7990 ],            
            [ 'id' => 12, 'membership_id' => 7, 'purchase_name' => 'Čtvrtletní členství', 'period_type' => 'quarter', 'price' => 4000 ],
            [ 'id' => 13, 'membership_id' => 8, 'purchase_name' => 'Měsíční členství', 'period_type' => 'month', 'price' => 990 ],
            [ 'id' => 14, 'membership_id' => 8, 'purchase_name' => 'Roční členství', 'period_type' => 'year', 'price' => 9990 ],
            [ 'id' => 15, 'membership_id' => 9, 'purchase_name' => 'Zkušební členství', 'period_type' => null, 'price' => 1590 ],
            [ 'id' => 16, 'membership_id' => 10, 'purchase_name' => 'Dobíjecí karta', 'period_type' => null, 'price' => 1000 ]                                                               
        ];

        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 0');
        $truncateMembershipTypes = $this->query('truncate membership_types');
        $truncateMembershipPrices = $this->query('truncate membership_prices');
        $truncateMembership = $this->query('truncate membership');
        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 1');
        $membershipTypes = $this->table('membership_types');
        $membership = $this->table('membership');
        $membershipPrices = $this->table('membership_prices');
        $membershipTypes->insert($membershipTypesData)->save();
        $membership->insert($membershipData)->save();
        $membershipPrices->insert($membershipPricesData)->save();
    }
}
