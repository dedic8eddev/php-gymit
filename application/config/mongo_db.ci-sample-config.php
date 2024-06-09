<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['mongo_db']['active'] = 'default';

$config['mongo_db']['default']['no_auth'] = false;
$config['mongo_db']['default']['hostname'] = _ci-sample-config_item['hostname'];
$config['mongo_db']['default']['port'] = _ci-sample-config_item['port'];
$config['mongo_db']['default']['username'] = _ci-sample-config_item['username'];
$config['mongo_db']['default']['password'] = _ci-sample-config_item['password'];
$config['mongo_db']['default']['database'] = _ci-sample-config_item['database'];
$config['mongo_db']['default']['db_debug'] = TRUE;
$config['mongo_db']['default']['return_as'] = 'array';
$config['mongo_db']['default']['write_concerns'] = (int)1;
$config['mongo_db']['default']['journal'] = TRUE;
$config['mongo_db']['default']['read_preference'] = 'primary'; 
$config['mongo_db']['default']['read_concern'] = 'local'; //'local', 'majority' or 'linearizable'
$config['mongo_db']['default']['legacy_support'] = TRUE;