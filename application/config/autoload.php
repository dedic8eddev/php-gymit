<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = [];
$autoload['libraries'] = [
    'ion_auth',
    'database',
    'email',
    'session',
    'user_agent',
    'form_validation',
    'gymit_app' => 'app',
    'gymit_components' => 'app_components',
    'gymit_blocks' => 'app_blocks',
    'gymit_databases' => 'gymdb',
    'mailgun',
    'mongo_db',
    'EETApp_api' => 'eetapp_lib',
    'GPwebpay' => 'gpwebpay',
    'mPDF' => 'mpdf',
    'TMPrinter' => 'tmprinter'
];
$autoload['drivers'] = [];
$autoload['helper'] = [
    'url',
    'form',
    'cookie',
    'date',
    'basic_helper',
    'auth_helper',
    'permission_helper',
    'db_helper',
    'text',
];
$autoload['config'] = ['app_config', 'lang_config', 'api_config'];
$autoload['language'] = [];
$autoload['model'] = [
    'notifications_model' => 'n',
    'gyms_model'=>'gyms',
    'settings_model' => 'settings',
    'payments_model' => 'payments',
    'autocont_model' => 'autocont',
    'api_model' => 'API',
    'cards_model' => 'cards'
];
