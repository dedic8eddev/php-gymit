<?php

/**
 * ENVIRONMENT SEPARATE PHP FILE FOR CLEARANCE
 */

if(! defined('ENVIRONMENT') ){
    if(isset($_SERVER['HTTP_HOST'])){
        $domain = strtolower($_SERVER['HTTP_HOST']);
        switch($domain) {
            case 'localhost:8000' :
                define('ENVIRONMENT', 'development');
            break;
    
            default :
                define('ENVIRONMENT', 'production');
            break;
        }
    }else{
        define('ENVIRONMENT', 'production');
    }
}