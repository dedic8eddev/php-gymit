<?php


use Phinx\Seed\AbstractSeed;

class GymSettingsSeeder extends AbstractSeed
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
        // gym settings data
        $gymSettingsData = [
            [
                'id'             => 1,
                'type'           => 'opening_hours',
                'data'           => '{"monday":{"from":"06:00","to":"22:00"},"saturday":{"from":"08:00","to":"20:00"},"holiday":{"from":"12:00","to":"18:00"}}',
            ],
            [
                'id'             => 2,
                'type'           => 'general_info',
                'data'           => '{"street":"Masarykova 592/93","city":"Ústí nad Labem","zip":"400 01","country":"CZ","email":"hello@gymit.cz","phone":"+420 731 103 598","fb_link":"fb_link","ig_link":"ig_link"}',
            ],
            [
                'id'             => 3,
                'type'           => 'footer',
                'data'           => '[{"logo":"","text":"A successful marketing plan relies heavily on the pulling-power of advertising copy. Writing result-oriented ad copy is difficult, as it must appeal to, entice, and convince consumers to take action."},{"links":[{"link":"/membership","text":"\u010cLENSTV\u00cd A CEN\u00cdK"},{"link":"/calendar","text":"ROZVRH A REZERVACE"},{"link":"/services","text":"SLU\u017dBY"},{"link":"/cocahes","text":"NA\u0160I TREN\u00c9\u0158I"},{"link":"/lessons","text":"SKUPINOVÉ LEKCE"},{"link":"/jobs","text":"PRÁCE V GYMITU"},{"link":"/kontakt","text":"KONTAKT"}]},{"address":"Street 1, Town<br \/> Po - P\u00e1 06:00 - 22:00, So - Ne 08:00 - 20:00 ","email":"","phone":"","social_icons":""}]',
            ],
            [
                'id'             => 4,
                'type'           => 'page_homepage',
                'data'           => '{  "name":"Homepage", 
                                        "blocks":["block_membership","block_free_entry","block_newsletter"],
                                        "header_img":"",                    
                                        "header_title":"My body, my confidence, my happiness, my gym", 
                                        "header_subtitle":"Premium fitness centrum v ústí nad labem",
                                        "header_btn_text":"Chci být členem",
                                        "header_btn_url":"#",
                                        "services_title":"Služby",
                                        "services_subtitle":"Advertising Secrets",
                                        "news_title":"Aktuality",
                                        "news_subtitle":"Novinky ze světa Fitness",
                                        "coaches_title":"Naši trenéři",
                                        "coaches_subtitle":"Effective forms advertising internet web site",
                                        "lessons_title":"Skupinové lekce",
                                        "lessons_subtitle":"S profesionálními lektory a vybavením",
                                        "lessons_btn_text":"Do přehledu všech lekcí",
                                        "instagram_title":" aktuální dění v gymit premium fitness"
                                    }',
            ],
            [
                'id'             => 5,
                'type'           => 'page_coaches',
                'data'           => '{  "name":"Trenéři",
                                        "blocks":["block_newsletter"],                    
                                        "icon_image":"2",
                                        "cover_image":"/public/assets/img/temp/services_02.png",
                                        "header_image":"/public/assets/img/header_osobni_trener.png",
                                        "header_title":"Trenéři",
                                        "header_btn_text":"Naši trenéři",
                                        "header_btn_url":"#",
                                        "perex":"The collapse of the online-advertising market in 2001 made marketing on the Internet seem even less compelling.",
                                        "coaches_title":"Naši trenéři",
                                        "coaches_subtitle":"Effective forms advertising internet web site"                                
                                    }',
            ],
            [
                'id'             => 6,
                'type'           => 'page_services',
                'data'           => '{  "name":"Služby",
                                        "blocks":["block_newsletter"], 
                                        "header_image":"/public/assets/img/header_default.png",                    
                                        "header_title":"Služby"
                                    }',
            ],
            [
                'id'             => 7,
                'type'           => 'page_lessons',
                'data'           => '{  "name":"Lekce",
                                        "blocks":["block_newsletter"], 
                                        "icon_image":"3",
                                        "cover_image":"/public/assets/img/temp/services_03.png",
                                        "header_image":"/public/assets/img/header_cvicebni_zony.png",                 
                                        "header_title":"Skupinové lekce",
                                        "perex":"The collapse of the online-advertising market in 2001 made marketing on the Internet seem even less compelling."
                                    }',
            ], 
            [
                'id'             => 8,
                'type'           => 'block_membership',
                'data'           => '{
                                        "name": "Členství",
                                        "membership_title": "Typy členství",
                                        "membership_subtitle": "Effective forms advertising ninternet web site",
                                        "membership": {
                                            "1": {
                                                "header": "Na zkoušku",
                                                "code": "trial",
                                                "memDescription": "",
                                                "priceFromText": "",
                                                "price": "1.590",
                                                "periodText": "na měsíc",
                                                "description": "Měsíční neomezený vstup:",
                                                "icon": {
                                                    "1": "1",
                                                    "2": "3",
                                                    "3": "4"
                                                },
                                                "iconText": {
                                                    "1": "Cvičební zóny",
                                                    "2": "Skupinové lekce",
                                                    "3": "Wellness"
                                                },
                                                "iconPriceText": {
                                                    "1": "",
                                                    "2": "",
                                                    "3": ""
                                                },
                                                "btnText": "to chci"
                                            },
                                            "2": {
                                                "header": "předplacené",
                                                "code": "prepaid",  
                                                "memDescription": "",                                              
                                                "priceFromText": "od",
                                                "price": "500",
                                                "periodText": "1 dobití",
                                                "description": "Vstup:",
                                                "icon": {
                                                    "1": "1",
                                                    "2": "3",
                                                    "3": "4"
                                                },
                                                "iconText": {
                                                    "1": "Cvičební zóny",
                                                    "2": "Skupinové lekce",
                                                    "3": "Wellness po cvičení"
                                                },
                                                "iconPriceText": {
                                                    "1": "140 Kč",
                                                    "2": "140 Kč",
                                                    "3": "90 Kč"
                                                },
                                                "btnText": "to chci"
                                            },
                                            "3": {
                                                "header": "basic",
                                                "code": "basic",    
                                                "memDescription": "",                                            
                                                "priceFromText": "od",
                                                "price": "690",
                                                "periodText": "měsíčně",
                                                "description": "Vstup:",
                                                "icon": {
                                                    "1": "1",
                                                    "2": "3",
                                                    "3": ""
                                                },
                                                "iconText": {
                                                    "1": "Cvičební zóny",
                                                    "2": "Skupinové lekce"
                                                },
                                                "iconPriceText": {
                                                    "1": "",
                                                    "2": ""
                                                },
                                                "btnText": "to chci"
                                            },
                                            "4": {
                                                "header": "platinum",
                                                "code": "platinum",   
                                                "memDescription": "",                                             
                                                "priceFromText": "od",
                                                "price": "1.290",
                                                "periodText": "měsíčně",
                                                "description": "Neomezený vstup:",
                                                "icon": {
                                                    "1": "1",
                                                    "2": "3",
                                                    "3": "4"
                                                },
                                                "iconText": {
                                                    "1": "Cvičební zóny",
                                                    "2": "Skupinové lekce",
                                                    "3": "Wellness po cvičení"
                                                },
                                                "iconPriceText": {
                                                    "1": "",
                                                    "2": "",
                                                    "3": ""
                                                },
                                                "btnText": "to chci"
                                            }
                                        }
                                    }'
            ],         
            [
                'id'             => 9,
                'type'           => 'page_exercise_zones',
                'data'           => '{"name":"Cvičební zóny",
                                        "service_type": "1",
                                        "blocks":["block_exercise_zones_equipment","block_newsletter"],
                                        "header_title":"Cvičební zóny",
                                        "perex":"The collapse of the online-advertising market in 2001 made marketing on the Internet seem even less compelling.",
                                        "text":"ASDASDASD",
                                        "icon_image":"1",
                                        "cover_image":"/public/assets/img/temp/services_01.png",
                                        "header_image":"/public/assets/img/header_cvicebni_zony.png",
                                        "news_title":"Udělejte něco pro své zdraví",
                                        "news_subtitle":"Advertising secrets"
                                    }'
            ],
            [
                'id'             => 10,
                'type'           => 'block_exercise_zones_equipment',
                'data'           => '{"name":"Vybavení Cvičebních zón",
                                        "images":[
                                            "/public/assets/img/temp/fullgallery_01.png",
                                            "/public/assets/img/temp/fullgallery_02.png",
                                            "/public/assets/img/temp/fullgallery_03.png",
                                            "/public/assets/img/temp/fullgallery_01.png",
                                            "/public/assets/img/temp/fullgallery_02.png",
                                            "/public/assets/img/temp/fullgallery_03.png"
                                        ]
                                    }'
            ],                 
            [
                'id'             => 11,
                'type'           => 'page_wellness',
                'data'           => '{"name":"Wellness",
                                        "service_type": "4",
                                        "blocks":["block_wellness_equipment","block_newsletter"],
                                        "header_title":"Wellness",
                                        "perex":"The collapse of the online-advertising market in 2001 made marketing on the Internet seem even less compelling.",
                                        "text":"text",
                                        "icon_image":"4",
                                        "cover_image":"/public/assets/img/temp/services_04.png",
                                        "header_image":"/public/assets/img/header_wellness.png",
                                        "news_title":"Udělejte něco pro své zdraví",
                                        "news_subtitle":"Advertising secrets"
                                    }'
            ],
            [
                'id'             => 12,
                'type'           => 'block_wellness_equipment',
                'data'           => '{"name":"Vybavení wellness",
                                        "images":[
                                            "/public/assets/img/temp/fullgallery_01.png",
                                            "/public/assets/img/temp/fullgallery_02.png",
                                            "/public/assets/img/temp/fullgallery_03.png",
                                            "/public/assets/img/temp/fullgallery_01.png",
                                            "/public/assets/img/temp/fullgallery_02.png",
                                            "/public/assets/img/temp/fullgallery_03.png"
                                        ]
                                    }'
            ],              
            [
                'id'             => 13,
                'type'           => 'block_free_entry',
                'data'           => '{"name":"První vstup zdarma",                 
                                        "title":"První vstup zdarma",
                                        "subtitle":"Effective forms advertising ninternet web site",
                                        "text":"In this digital generation where information can be easily obtained within seconds, business cards still have retained their importance in the achievement of increased business exposure and business sales.",
                                        "btn_url":"#",
                                        "btn_text":"Chci vstup zdarma"                                        
                                    }'
            ],       
            [
                'id'             => 14,
                'type'           => 'page_pricelist',
                'data'           => '{"name":"Členství a ceník",
                                        "blocks":["block_membership","block_pricelist","block_newsletter"],
                                        "header_image":"/public/assets/img/header_default.png",                    
                                        "header_title":"Členství a ceník",
                                        "membership_title":"Předplatné členství",
                                        "pricelist_title":"Jednorázové vstupné"                                    
                                    }'
            ],
            [
                'id'             => 15,
                'type'           => 'block_pricelist',
                'data'           => '{"name":"Ceník",
                                        "title":"Ceník",
                                        "subtitle":"Effective forms advertising ninternet web site"
                                    }'
            ],               
            [
                'id'             => 16,
                'type'           => 'page_contact',
                'data'           => '{"name":"Kontakt",
                                        "blocks":["block_newsletter"],
                                        "header_image":"/public/assets/img/header_default.png",                    
                                        "header_title":"Kontakt",
                                        "header_contact_title":"Kontaktujte nás",
                                        "header_location_title":"Kde nás najdete",
                                        "header_opening_hours_title":"Otevírací hodiny",
                                        "find_us_title":"Kde nás najdete?",
                                        "find_us_text":"<p>There are several ways people can make money online. From selling products to advertising:</p>",
                                        "find_us_bus":"10, 27, 16 (zastávka Lázne) +",
                                        "find_us_bus_walk":"5 min",
                                        "find_us_car":"Parkoviště D +",
                                        "find_us_car_walk":"1 min",                                        
                                        "operator_title":"Provozovatel",
                                        "operator_text":"<p>CS Fitness s.r.o., Elišky Peškové 735/15, 150 00 Praha</p><p>IČO: 05142067</p>",
                                        "contact_form_title":"Napište nám",
                                        "contact_form_subtitle":"náš tým je tu pro vás"
                                    }'
            ],   
            [
                'id'             => 17,
                'type'           => 'page_calendar',
                'data'           => '{"name":"Rozvrh a rezervace",
                                        "blocks":["block_newsletter"],
                                        "header_image":"/public/assets/img/header_default.png",                    
                                        "header_title":"Rozvrh a rezervace"
                                    }'
            ],
            [
                'id'             => 18,
                'type'           => 'page_jobs',
                'data'           => '{"name":"Práce v gymitu",
                                        "header_image":"/public/assets/img/header_default.png",                    
                                        "header_title":"Práce v gymitu",
                                        "jobs_title":"Přidej se k gymit týmu",
                                        "jobs_subtitle":"Effective forms advertising ninternet web site",
                                        "another_job_title":"Nemáme pro tebe otevřenou pozici? ",
                                        "another_job_subtitle":"Neustále hledáme proaktivní lidi do našeho týmu",
                                        "another_job_text":"Despite growth of the Internet over the past seven years, the use of toll-free phone numbers in television advertising continues to grow, indicating that the telephone remains a prevalent response tool, according to a recent study.",
                                        "another_job_email":"hr@gymit.cz"
                                    }'
            ],
            [
                'id'             => 19,
                'type'           => 'page_job_detail',
                'data'           => '{"name":"Detail práce v gymitu",
                                        "header_image":"/public/assets/img/header_default.png",
                                        "hire_title":"Pošli nám své CV",
                                        "hire_email":"hr@gymit.cz"
                                    }'
            ],
            [
                'id'             => 20,
                'type'           => 'block_newsletter',
                'data'           => '{"name":"Newsletter",
                                        "title":"Staňte se členem",
                                        "subtitle":"Gymit premium fitness",
                                        "priceFromText":"Od",
                                        "price":890,
                                        "priceDescText":"Kč měsíčně",
                                        "btn_url":"#",
                                        "btn_text":"Chci být členem"
                                    }'
            ],  
            [
                'id'             => 21,
                'type'           => 'front_menu_items',
                'data'           => '{"name":"Položky menu",
                                        "items" : {
                                            "1": { "name":"Členství a ceník", "url":"/membership", "show":"on" },
                                            "2": { "name":"Rozvrh a rezervace", "url":"/calendar", "show":"on" },
                                            "3": { "name":"Služby", "url":"/services", "show":"on" },
                                            "4": { "name":"Naši trenéři", "url":"/coaches", "show":"on" },
                                            "5": { "name":"Skupinové lekce", "url":"/lessons", "show":"on" },
                                            "6": { "name":"Práce v gymitu", "url":"/jobs", "show":"on" },
                                            "7": { "name":"Kontakt", "url":"/kontakt", "show":"on" }
                                        }
                                    }'
            ],
            [
                'id'             => 22,
                'type'           => 'subject_info',
                'data'           => '{
                                        "name":"CS Fitness, s.r.o.",
                                        "company_id":"05142067",
                                        "vat_id":"Town",
                                        "street":"Elišky Peškové 735/15",
                                        "town":"Praha 5 - Smíchov",
                                        "zip":"150 00",
                                        "bank_account":"291212339/0300"
                                    }',
            ]
        ];
        foreach ($gymSettingsData as $k=>$v){
            $gymSettingsData[$k]['data']=preg_replace('/\s{2,}/','',$v['data']); // delete whitespaces
        }
        $rows = $this->query('truncate gym_settings');
        $table = $this->table('gym_settings');
        $table->insert($gymSettingsData)->save();
    }
}
