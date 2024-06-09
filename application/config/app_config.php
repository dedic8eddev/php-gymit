<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base APP config
*/

// Basic
$config['app']['site_title']                        = 'Gymit';

$config["app"]["maintenance_ip_addresses"] = [
    "127.0.0.1", // Localhost
    "212.20.107.194" // Bold office
];

// Blind copy email addresses
$config['app']['bccEmails'] = ENVIRONMENT == 'development' ? 'h.havelka@seznam.cz' : 'hello@gymit.cz, obchod@gymit.cz, michal@bold-interactive.com';

$config['app']['services']                          = [
    1 => 'Cvičební zóny',
    2 => 'Osobní trenér',
    3 => 'Skupinové lekce',
    4 => 'Wellness',
    5 => 'Solárium',
    6 => 'Vouchery',
    7 => 'Půjčovna',
    10 => 'Ostatní',
    11 => 'Parkování',
];
$config['app']['services_subtypes']                 = [
    1 => 'Jednorázový vstup',
    2 => 'Poplatek',
];
// lessons_duration (needed for price_list)
$config['app']['lessons_duration']                  = [
    '01:00:00' => '60 minut',
    '01:30:00' => '90 minut',
    '00:30:00' => '30 minut'
];
// maximum hours before lesson starts to get reservation fee back
$config['app']['lesson_reservervation_refund_hours']= 8;

$config['app']['vat_values']                        = [
    '0.15' => '15%',
    '0.21' => '21%'
];
$config['app']['depot_item_units']                  = ['ks', 'kg', 'g', 'l', 'dcl', 'ml'];
$config['app']['depot_item_categories']             = [
    1 => 'Výživa',
    2 => 'Nápoje',
    6 => 'Ostatní'
];

$config['app']['gym_rooms_categories']              = [
    1 => 'Fitness',
    2 => 'Relaxace',
    3 => 'Skupinové lekce',
    4 => 'Vstup'
];

$config['app']['invoice_due_days']                  = 25;

$config['app']['global_tables']                     = [
    'users',
    'groups',
    'users_groups',
    'users_data',
    'users_tokens',
    'users_cards',
    'login_attempts',
    'countries',
    'membership_types',
    'membership',
    'phinxlog'
]; // MySQL tables shared between gyms

$config['app']['custom_field_sections']             = [
    'users' => 'Uživatelé',
    'depot_items' => 'Sklad'
];
$config['app']['custom_field_types']                = [
    'text' => 'Text',
    'number' => 'Číslo',
    'select' => 'Výběr'
];

// Identification types
$config['app']['identification_types']              = [
    '1' => 'Občanský průkaz',
    '2' => 'Řidičský průkaz',
    '3' => 'Pas',
];

// Assets
$config['app']['js_folder']                         = 'public/assets/js/'; // cusotm js
$config['app']['js_lib_folder']                     = 'public/assets/js/libs/'; // js libraries
$config['app']['css_folder']                        = 'public/assets/css/'; // custom css
$config['app']['css_lib_folder']                    = 'public/assets/css/libs/'; // css libraries
$config['app']['img_folder']                        = 'public/assets/img/'; // img folder

// Roles
$config['app']['roles']                             = [null,1, 2, 3, 4, 5, 6, 7, 8,9,10,11,12,20,21];
$config['app']['roles_names']                       = [
    null,
    1 => "Administrátor",
    2 => "Manažer provozovny",
    3 => "Senior recepční",
    4 => "Recepční",
    5 => "Obsluha wellness",
    6 => "Pracovník dětského koutku",
    7 => "Správce webu",
    8 => "Gym a studio manažer",
    9 => "Master trainer",
    10 => "Osobní trenér",
    11 => "Instruktor",
    12 => "Servisní technik",
    20 => "Klient",
    21 => "Jednorázový uživatel"
];

$config['app']['disposable_user_password']          = 'gymitPass2020';

// Dates
$config['app']['monthsCZ']                          = [null,'Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec'];
$config['app']['weekdaysCZ']                        = ['Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota', 'Neděle'];

// Directories
$config['app']['temp_files']                        = 'public/temp/';
$config['app']['user_files']                        = 'public/users_files/';
$config['app']['lessons_images']                    = 'public/lessons/';
$config['app']['blog_images']                       = 'public/blog/';
$config['app']['media']['folder']                   = 'public/media/';
$config['app']['media']['thumbs']                   = 'public/media/thumbs/';

// Autocont directories and stuff
$config['app']['autocont_folder']                   = 'autocont/';
$config["app"]["autocont_marketing_account"]        = "510.311";
$config["app"]["autocont_accounts"]                 = [
    ["name" => "Tržby za členství", "value" => "602.111"],
    ["name" => "Tržby z předplacených karet", "value" => "602.112"],
    ["name" => "Tržby za realizované osobní tréninky", "value" => "602.113"],
    ["name" => "Tržby za jednorázové vstupy", "value" => "602.114"],
    ["name" => "Tržby za vydání karty", "value" => "602.311"],
    ["name" => "Tržby za pronájem", "value" => "602.411"],
    ["name" => "Tržby za ostatní služby fitcentra", "value" => "602.511"],
    ["name" => "Tržby za parking", "value" => "602.611"],
    ["name" => "Tržby za dětský koutek", "value" => "602.711"],
    ["name" => "Tržby z prodeje občerstvení (nápoje+výživa)", "value" => "604.111"],
    ["name" => "Tržby z prodeje ostatního zboží", "value" => "604.112"],
    ["name" => "Tržby za marketingové účely", "value" => "510.311"]
]; // This might need a rework sooner or later, but for now..

// EETApp
$config['app']['eetapp']['api']                        = 'https://bold-interactive-dev.eetapp.cz/api';
$config['app']['eetapp']['username']                   = 'dev';
$config['app']['eetapp']['password']                   = 'YgBlT8';

// Lessons
$config['app']['lesson_reservation_fee']            = 50;

// Default assets
$config['app']['backend']['assets']['css']                     = [
    'animate.css/animate.min.css',
    'material-design-iconic-font/dist/css/material-design-iconic-font.css',
    'font-awesome/css/font-awesome.min.css',
    'fullcalendar.css',
    'template/bootstrap.css',
    'template/app.css',
    'noty.css',
    'noty.bs3.css',
    'npg.css'
];
$config['app']['backend']['assets']['js']                      = [
    'jquery/dist/jquery.js',
    'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js',
    'jquery.badge.js',
    'template/app.js',
    'moment/moment.js',
    'noty.min.js',
    'select2.min.js',
    'select2.cs.js',
    'npg.js',
    'admin._main.js',
    'admin._personificators.js',
    'admin._notifications.js'
];

// Frontend assets
$config['app']['frontend']['assets']['css']                     = [
    'animate.css/animate.min.css',
    'front.app.css',
    'front.custom.css',
    'noty.css',
    'noty.frontend.css'
];

$config['app']['frontend']['assets']['js']                      = [
    'jquery/dist/jquery.js',
    'jquery/dist/jquery.validate.js',    
    'popper.min.js',    
    'http://maps.google.com/maps/api/js?key=AIzaSyDAtPrVuoR8guTnX_8Xi4AIj-P1kICdElw',
    'slick.min.js',
    'isotope.pkgd.min.js',
    'moment/moment.js',
    'noty.min.js',
    'tippy.min.js',    
    'front._main.js',
    'front._notifications.js',
    'front.app.main.js'
];

$config['app']['svg_icons']                                    = [
    'pricelist' => [
        1 => '<svg enable-background="new 0 0 57 33" version="1.1" viewBox="0 0 57 33" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><rect class="st0" x="16.5" y="12.5" width="24" height="8"></rect><defs><rect width="57" height="33"></rect></defs><path d="m44.5 32.5c-2.2 0-4-1.8-4-4v-24c0-2.2 1.8-4 4-4s4 1.8 4 4v24c0 2.2-1.8 4-4 4z"></path><path d="m52.5 28.5c-2.2 0-4-1.8-4-4v-16c0-2.2 1.8-4 4-4s4 1.8 4 4v16c0 2.2-1.8 4-4 4z"></path><path d="m12.5 32.5c2.2 0 4-1.8 4-4v-24c0-2.2-1.8-4-4-4s-4 1.8-4 4v24c0 2.2 1.8 4 4 4z"></path><path d="m4.5 28.5c2.2 0 4-1.8 4-4v-16c0-2.2-1.8-4-4-4s-4 1.8-4 4v16c0 2.2 1.8 4 4 4z"></path></svg>',
        2 => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 60 49" style="enable-background:new 0 0 60 49;" xml:space="preserve"><g><defs><rect width="60" height="49" /></defs><rect x="48" y="26.5" width="8" height="22" /><rect x="4" y="26.5" width="8" height="22" /><path d="M52,26.5v-19c0-3.9-3.1-7-7-7s-7,3.1-7,7v25c0,4.4-3.6,8-8,8s-8-3.6-8-8v-25c0-3.9-3.1-7-7-7s-7,3.1-7,7v19" /><line x1="44" y1="26.5" x2="60" y2="26.5" /><line x1="0" y1="26.5" x2="16" y2="26.5" /><line x1="48" y1="40.5" x2="56" y2="40.5" /><line x1="4" y1="40.5" x2="12" y2="40.5" /></g></svg>',
        3 => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 57 47" style="enable-background:new 0 0 57 47;" xml:space="preserve"><g><defs><rect width="57" height="47" /></defs><path d="M36.2,12.7c2.5-2.2,5.3-4.1,8.6-5.4c1.7,4.2,2.5,8.6,2.3,12.9" /><line x1="30.8" y1="35.4" x2="30.8" y2="35.4" /><path d="M10,20.2c-0.2-4.3,0.5-8.6,2.3-12.9c3.2,1.3,6.1,3.2,8.6,5.4" /><path d="M38,25.5c0.3-4.3-0.3-8.7-1.8-12.8c-1.6-4.5-4.1-8.6-7.7-12.2c-3.6,3.6-6.1,7.7-7.7,12.2 c-1.5,4.1-2.1,8.5-1.8,12.8" /><path d="M38,25.5c0.3-4.3-0.3-8.7-1.8-12.8c-1.6-4.5-4.1-8.6-7.7-12.2c-3.6,3.6-6.1,7.7-7.7,12.2 c-1.5,4.1-2.1,8.5-1.8,12.8" /><path d="M56.5,18.5c0,15.5-12.5,28-28,28C28.5,31,41,18.5,56.5,18.5z" /><path d="M56.5,18.5c0,15.5-12.5,28-28,28C28.5,31,41,18.5,56.5,18.5z" /><path d="M56.5,18.5c0,15.5-12.5,28-28,28C28.5,31,41,18.5,56.5,18.5z" /><path cd="M0.5,18.5c0,15.5,12.5,28,28,28C28.5,31,16,18.5,0.5,18.5z" /><path d="M0.5,18.5c0,15.5,12.5,28,28,28C28.5,31,16,18.5,0.5,18.5z" /></g></svg>',
        4 => '<svg version="1.1" id="icons" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><path class="st0" d="M60,32c0,1.4-2.6,2.6-2.8,4c-0.2,1.4,1.9,3.3,1.4,4.7c-0.4,1.3-3.3,1.7-3.9,2.9c-0.6,1.3,0.7,3.8-0.1,4.9 C53.8,49.6,51,49,50,50c-1,1-0.4,3.8-1.6,4.6c-1.1,0.8-3.6-0.6-4.9,0.1c-1.2,0.6-1.6,3.5-2.9,3.9c-1.3,0.4-3.3-1.7-4.7-1.4 c-1.4,0.2-2.6,2.8-4,2.8c-1.4,0-2.6-2.6-4-2.8c-1.4-0.2-3.3,1.9-4.7,1.4c-1.3-0.4-1.7-3.3-2.9-3.9c-1.3-0.6-3.7,0.7-4.9-0.1 C14.4,53.8,15,51,14,50c-1-1-3.8-0.4-4.6-1.6c-0.8-1.1,0.6-3.6-0.1-4.9c-0.6-1.2-3.5-1.6-3.9-2.9C4.9,39.3,7,37.4,6.8,36 C6.6,34.6,4,33.4,4,32c0-1.4,2.6-2.6,2.8-4c0.2-1.4-1.9-3.3-1.4-4.7c0.4-1.3,3.3-1.7,3.9-2.9c0.6-1.3-0.7-3.7,0.1-4.9 C10.2,14.4,13,15,14,14c1-1,0.4-3.8,1.6-4.6c1.1-0.8,3.6,0.6,4.9-0.1c1.2-0.6,1.6-3.5,2.9-3.9C24.7,4.9,26.6,7,28,6.8 C29.4,6.6,30.6,4,32,4c1.4,0,2.6,2.6,4,2.8c1.4,0.2,3.3-1.9,4.7-1.4c1.3,0.4,1.7,3.3,2.9,3.9c1.3,0.6,3.8-0.7,4.9,0.1 C49.6,10.2,49,13,50,14c1,1,3.8,0.4,4.6,1.6c0.8,1.1-0.6,3.6,0.1,4.9c0.6,1.2,3.5,1.6,3.9,2.9c0.4,1.3-1.7,3.3-1.4,4.7 C57.4,29.4,60,30.6,60,32z" /><path class="st0" d="M50.1,32c0,10-8.1,18.1-18.1,18.1C22,50.1,13.9,42,13.9,32S22,13.9,32,13.9C42,13.9,50.1,22,50.1,32z" /></g><path class="st1" d="M34,26.6c-3.6,0-6.3,2.3-6.3,6.5c0,3.6,2.1,4.4,4.4,4.4c1.2,0,2.3-0.3,3.4-1l0.3-1.7h-4.5l0.6-3.7h9.1l-1.1,7.1 c-1.6,2.4-5.3,3.7-8.9,3.7c-5.6,0-9.4-3.6-8.7-9.6c0.7-6.5,5.8-10.2,12.4-10.2c2.8,0,5.1,0.7,6.9,2.8l-0.8,4.5h-4.4l0.2-2.3 C35.9,26.7,34.8,26.6,34,26.6z" /></svg>'
    ]
];