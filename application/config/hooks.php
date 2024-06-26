<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'] = function() {
    $dotenv = Dotenv\Dotenv::createImmutable(APPPATH);
    $dotenv->load();
};

$hook['post_controller_constructor'][] = array(
    'class'    => 'Gymit_Assets_Hook',
    'function' => 'assets',
    'filename' => 'Gymit_assets_hook.php',
    'filepath' => 'hooks',
    'params'   => array()
);