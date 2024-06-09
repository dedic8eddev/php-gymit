<?php

/**
	 * Konfigurace aplikace podle režimů ve kterém se nachází - development, testing, production
     * 
     * Hlavní konfigurační soubor celé aplikace
     * 
     * Autor: Denis Matyjaško
     * Email: denismatyjasko@gmail.com
     * Version: 1.0
	 */

    if(empty($_GET)){
        parse_str(implode('&', array_slice($argv, 1)), $_GET);
    }

     // povolené režimy aplikace
     if(!defined('ALLOWED_ENVRIRONMENT')){
        define('ALLOWED_ENVRIRONMENT',serialize(array('development', 'bold', 'dev','production')));
     }
     // nastavení cesty ke konfiguračnímu souboru
     if(!defined('CI_APP_CONFIG_PARAMETERS')){
        define('CI_APP_CONFIG_PARAMETERS',dirname(__FILE__).'/application/config/ci_app_config.json');
     }
     // cílová složka konfigurovatelných souborů
     if(!defined('FOLDER_DESTINATION')){
        define('FOLDER_DESTINATION',dirname(__FILE__).'/application/config/');
     }
     //suffix konfigurovatelného souboru
     if(!defined('CONFIG_FILE_SUFIX')){
        define('CONFIG_FILE_SUFIX','ci-sample-config');
     }
     //temp suffix konfigurovatelného souboru
     if(!defined('CONFIG_FILE_SUFIX_TEMP')){
        define('CONFIG_FILE_SUFIX_TEMP','ci-sample-config-temp');
     }
     // nahrazovaný řetězec při konfiguraci
     if(!defined('REPLACE_STRING')){
        define('REPLACE_STRING','_ci-sample-config_item');
     }
     // index.php
     if(!defined('INDEX')){
        define('INDEX',dirname(__FILE__).'/index.php');
     }

     // aktuálně nastavený režim aplikace
     $environment = '';
     // načti obsah index souboru
     $indexContent = file_get_contents(INDEX);
     //echo $indexContent;

     foreach(unserialize(ALLOWED_ENVRIRONMENT) as $env){
         if(strpos($indexContent,"\$environment = '".$env."'") !== FALSE){
            $environment = $env;
            break;
         }
     }

     if(!empty($environment)){
         // aktuálně nastavený režim aplikace
        if(!defined('CURRENT_ENVRIRONMENT')){
            define('CURRENT_ENVRIRONMENT',$environment);
         }
     }else{
         echo("Error: Nebylo možné identifikovat envrironment aplikace.". PHP_EOL);
         die();
     }

    if(isset($_GET['set_envrironment'])){
        setEnvironment($_GET['set_envrironment']);
    }

    if(isset($_GET['run'])){
        run($_GET['run']);
    }

    if(isset($_GET['generate_config'])){
        generateConfig($_GET['generate_config']);
    }


    function run($mode = '')
	{

        // kontrola konfiguračního souboru
        if(checkConfig()){
            // init konfigurace
            if($mode = initConfig($mode)){

                // načtení obsahu konfiguračního souboru
                $config = file_get_contents(CI_APP_CONFIG_PARAMETERS);
                $config = json_decode($config);

                // projdi konfiguraci v definovaném modu
                foreach($config->$mode as $file => $params){

                    // kontrola zda-li se konfigurovatelný soubor obsahuje správný sufix
                    if(strpos($file,CONFIG_FILE_SUFIX) !== FALSE){

                        // kontrola existence souboru
                        if(file_exists(FOLDER_DESTINATION.$file)){

                            // název nového(temp) souboru - mezikrok pro zabezpečení
                            $fileTemp = str_replace('.'.CONFIG_FILE_SUFIX,'.'.CONFIG_FILE_SUFIX_TEMP,$file);
                            // název nového finálího souboru
                            $fileFinalName = str_replace('.'.CONFIG_FILE_SUFIX,'',$file);

                            // načti obsah konfigurovatelného souboru
                            $fileContent = file_get_contents(FOLDER_DESTINATION.$file);
                            //pole řetězců, které se budou nahrazovat
                            $replaceDestination= array();
                            // pole nahrazovaných hodnot
                            $replaceValue = array();

                            //projdi konfigurovatelné parametry v konfiguraci
                            foreach($params as $paramKey => $param){
                                // ulož nahrazované řetězce do pole
                                $replaceDestination[] = REPLACE_STRING."['".$paramKey."']";
                                // ulož hodnoty které nahradí řetězce do pole
                                $replaceValue[] = $param;
                            }

                            // vytvoř nový temp nakonfigurovaný soubor
                            if(file_put_contents(FOLDER_DESTINATION.$fileTemp,str_replace($replaceDestination,$replaceValue,$fileContent))){
                                
                                // přejmenuj temp soubor
                                if(rename(FOLDER_DESTINATION.$fileTemp,FOLDER_DESTINATION.$fileFinalName)){
                                    echo("Success: Konfigurace souboru '".$fileFinalName."' byla uspesne dokoncena.". PHP_EOL);
                                }else{
                                    echo("Error: Soubor '".$fileTemp."' se nepodarilo prejmenovat.");
                                    die();
                                }

                            }else{
                                echo("Error: Konfigurace souboru '".$fileFinalName."' nebyla uspensne dokoncena.". PHP_EOL);
                                die();
                            }

                        }else{
                            echo ("Error: Konfiguracni soubor '".$file."' neexistuje.". PHP_EOL);
                            die();
                        }

                    }else{
                        echo ("Error: Konfigurovatelny soubor '".$file."' neobsahuje sufix '".CONFIG_FILE_SUFIX."'". PHP_EOL);
                        die();
                    }
                }

            }else{
                die();
            }

        }else{
            die();
        }
    }

    /**
     * Nastavení Enviroment aplikace
     *
     * @param string $enviroment režim aplikace
     * @return bool true|false
     */
    function setEnvironment($enviroment)
    {

        // kontrola režimu
        if(in_array($enviroment,unserialize(ALLOWED_ENVRIRONMENT))){

            // získej předchozí stav enviromentu
            $oldEnviroment = CURRENT_ENVRIRONMENT;

            // kontrola zda-li se mění Enviroment aplikace
            if($oldEnviroment != $enviroment){
                // řetězec který bude nahrazen
                $oldReplaceString = "\$environment = '".$oldEnviroment."';";
                // nový řetězec který nahradí starý
                $newReplaceString = "\$environment = '".$enviroment."';";
                
                // načti obsah index souboru
                $indexContent = file_get_contents(INDEX);

                // vytvoř nový index.php s novým enviroment
                if(file_put_contents(INDEX,str_replace($oldReplaceString,$newReplaceString,$indexContent))){
                                
                    echo("Success: Nastaveni Enviroment aplikace bylo uspesne dokonceno.". PHP_EOL);
                    echo("Aplikace nastavena do rezimu: ".$enviroment. PHP_EOL);

                }else{
                    echo("Error: Nastaveni Enviroment aplikace nebylo uspesne dokonceno.". PHP_EOL);
                }
            }else{
                echo("Aplikace se jiz nachazi v rezimu: ".$enviroment. PHP_EOL);
            }

        }else{
            echo ("Error: Zadan neplatny rezim aplikace.". PHP_EOL);
        }
    }

    /**
     * Vygeneruje nový ci_app_config.json pouze s konfigurací podle zadaného envrironmentu
     *
     * @param string $envrironment režim aplikace (životní prostředí aplikace)
     * @return void
     */
    function generateConfig($envrironment)
    {
        if(checkConfig()){

            // kontrola zda-li je zadaný envrironment platný
            if(in_array($envrironment,unserialize(ALLOWED_ENVRIRONMENT))){
                // obsah konfiguračního souboru
                $config = file_get_contents(CI_APP_CONFIG_PARAMETERS);
                $config = json_decode($config);
                // doplňujicí režimy
                $complementaryEnvrironments = unserialize(ALLOWED_ENVRIRONMENT);

                if(($removeKey = array_search($envrironment,$complementaryEnvrironments)) !== false ){
                    unset($complementaryEnvrironments[$removeKey]);
                    
                    // kontrola zda-li hlavní konfiguační soubor obsahuje pouze již zvolený nakonfigurovaný envrironment
                    // pomocná kontrolní proměnná
                    $check = TRUE;
                    foreach($complementaryEnvrironments as $complementaryEnvrironment){
                        if(!empty((array)$config->$complementaryEnvrironment)){
                            $check = FALSE;
                            break;
                        }
                    }

                    if(!$check){
                        // nový obsah
                        $newContent = new stdClass();
                        $newContent->$envrironment = $config->$envrironment;
                        foreach($complementaryEnvrironments as $complementaryEnvrironment){
                            $newContent->$complementaryEnvrironment = new stdClass();
                        }
                        $newContent = json_encode($newContent);

                        // vygeneruj nový nakonfigurovaný soubor
                        if(file_put_contents(CI_APP_CONFIG_PARAMETERS,$newContent)){
                                    
                            echo("Success: Vygenerovani konfigurace bylo uspesne dokonceno.". PHP_EOL);
                            return TRUE;

                        }else{
                            echo("Error: Vygenerovani konfigurace nebylo uspensne dokonceno.". PHP_EOL);
                            return FALSE;
                        }
                    }else{
                        echo ("Success: Konfigurace je aktualni.". PHP_EOL);
                        return FALSE;
                    }

                }else{
                    echo ("Error: Nebylo možné identifikovat doplnujicí envrironment.". PHP_EOL);
                    die();
                }

            }else{
                // hlavní konfigurační soubor neexistuje
                echo ("Error: Zadán neplatny parametr.". PHP_EOL);
                die();
            }
        }else{
            die();
        }
    }

    // Inicializace konfigurace
    function initConfig($mode)
    {
        // pokud parametr režim je FALSE - použije se výchozí který je aktuálně nastavený
        $mode = $mode == '' ? CURRENT_ENVRIRONMENT : $mode;

        // kontrola zadaného režimu v povolených režimech
        if(in_array($mode,unserialize(ALLOWED_ENVRIRONMENT))){

            // kontrola režimu aplikace a konfigurace
            if(CURRENT_ENVRIRONMENT == $mode){
                echo ("Spousteny rezim konfigurace: ".$mode.".". PHP_EOL);
                return $mode;
             }else{
                 echo ("Error: Aplikace je nastavena v jinem rezimu, nez ve kterem spoustite konfiguraci. Nastavte spravny rezim aplikace.". PHP_EOL);
                 return FALSE;
             }

        }else{
            echo ("Error: Neplatny rezim konfigurace.". PHP_EOL);
            return FALSE;
        }
    }

    /**
     * Kontrola konfiguračního souboru aplikace
     * Kontrola existence
     * Kontrola nadefinovaných povinných envrironment aplikace
     *
     * @return void
     */
    function checkConfig()
    {
        // zkontrolovat existenci
        if(file_exists(CI_APP_CONFIG_PARAMETERS)){

            // hlavní konfigurační soubor existuje

            // obsah konfiguračního souboru
            $config = file_get_contents(CI_APP_CONFIG_PARAMETERS);
            $config = json_decode($config);
            
            // kontrola zda-li hlavní konfigurační soubor obsahuje nadefinované všechny typy režimů
            foreach(unserialize(ALLOWED_ENVRIRONMENT) as $rEnvrironment){
                if(!array_key_exists($rEnvrironment,$config)){
                    echo ("Error: Konfiguracni soubor neobsahuje nadefinované všechny povinné Envrironment aplikace.". PHP_EOL);
                    return FALSE;
                }
            }

            return TRUE;

        }else{
            // hlavní konfigurační soubor neexistuje
            echo ("Error: Konfiguracni soubor nebyl nalezen.". PHP_EOL);
            return FALSE;
        }
    }
