<?php

 /**
  * Pro uspěšný deploy je za potřebí mít vyplněný správně ci_app_config.json a mít přístupové údaje k serveru (zadávají se až při deployi)
  * Tento skript se spustí z tohoto adresáře pomocí '../vendor/bin/dep deploy production [-vvv --ansi]' (další parametry v [] jsou nepovinné)
  */

  /**
   *  Přes spuštěním deploy je prozatím potřeba nejdříve přidat práve pro csfitness group složce /var/www/html/dev
   *    sudo setfacl -m g:csfitness:rwx /var/www/html/dev
   * 
   *  A pak je zas odebrat!
   *    sudo setfacl -x g:csfitness /var/www/html/dev
   */

    namespace Deployer;
    require 'recipe/codeigniter.php';

    set('application', 'gymit');
    set('local_project_path', realpath( __DIR__ . '/..' ));
    set('repository', 'git@bitbucket.org:interactivebold/gymit.git');
    set('keep_releases', 3);

    set('git_tty', true);

    add('shared_files', []);
    add('shared_dirs', [
        "autocont",
        "public/uploads",
        "public/media/thumbs",
        "public/temp",
        "public/users_files",
        "public/lessons",
        "public/blog"
    ]);
    add('writable_dirs', get("shared_dirs"));

    // BOLD DEV
    // GYMIT.BOLDTESTING.CZ
    host('31.31.77.109')
        ->user('gymitgymit_shell')
        ->stage('bold')
        ->port(22)
        ->set('branch', 'bugfix/development') // dev branch
        ->set('deploy_path', '/var/www/clients/client4/web13/web');

    // PRODUCTION
    // GYMIT.CZ
    host('185.111.99.248')
        ->user('csfitness')
        ->stage('production')
        ->port(22)
        ->set('branch', 'master')
        ->set('writable_use_sudo', TRUE)
        ->set('deploy_path', '/var/www/html/dev');

    // CLIENT DEV
    // DEV.GYMIT.CZ
    host('89.221.217.94')
        ->user('root')
        ->stage('dev')
        ->port(22)
        ->set('branch', 'bugfix/development')
        ->set('writable_use_sudo', TRUE)
        ->set('deploy_path', '/var/www/html/dev');

    task('vendors', function () {
        if (commandExist('composer')) {
            $composer = 'composer';
        } else {
            run("cd {{release_path}} && curl -sS https://getcomposer.org/installer | php");
            $composer = 'php composer.phar';
        }

        run("cd {{release_path}} && $composer install");
        write("Downloaded Composer vendor files on remote host.");

        // Npm
        run("cd {{release_path}}/microservices/transactions && npm install");
        run("cd {{release_path}}/microservices/depot && npm install");

        if ( askChoice("Install IoT services? (make sure you have OS dependencies)", [0 => "No", 1 => "Yes"], 0, FALSE) == "Yes" ) {
            run("cd {{release_path}}/microservices/readers && npm install");
            run("cd {{release_path}}/microservices/lockers && npm install");
            run("cd {{release_path}}/microservices/terminals1 && npm install");
            run("cd {{release_path}}/microservices/terminals2 && npm install");
        }

        write("Installed node microservices on remote host.");
    });

    task("microservices", function(){

    });

    task('upload_config', function () {
        upload('{{local_project_path}}/application/config/ci_app_config.json', '{{release_path}}/application/config');
        write('Succesfully uploaded JSON configuration file.');
    });

    task('upload_env', function () {
        upload('{{local_project_path}}/application/.env', '{{release_path}}/application');
        write('Succesfully uploaded ENVIRONMENT file');
    }); 

    task('generate_configs', function () {
        $stage = get("stage");
        run("php {{release_path}}/ci_config.php set_envrironment=" . $stage);
        run("php {{release_path}}/ci_config.php run");
        write("Generated ".$stage." configs on remote host.");

        run("rm {{release_path}}/application/config/ci_app_config.json");
        write("Deleted placeholder configuration file.");
    });

    task('migrate', function () {
        run("cd {{release_path}} && vendor/bin/phinx migrate -c application/config/phinx.php");

        if ( askChoice("Run all seeds? (0 => Only runs permission seed)", [0 => "No", 1 => "Yes"], 0, FALSE) == "Yes" ) {
            write("Running seeds!");

            run("cd {{release_path}} && vendor/bin/phinx seed:run -s CountrySeeder -c application/config/phinx.php"); // country seeder
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s GroupSeeder -c application/config/phinx.php"); // group seeder
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s UserSeeder -c application/config/phinx.php"); // user seeder
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s FakeClientsSeeder -c application/config/phinx.php"); // fake client seeder
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s GymSettingsSeeder -c application/config/phinx.php"); // settings seeder
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s PricelistSeeder -c application/config/phinx.php"); // settings seeder
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s MembershipSeeder -c application/config/phinx.php"); // membership seeder
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s PermissionSeeder -c application/config/phinx.php"); // perm seeder
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s SiteSettingsSeeder -c application/config/phinx.php"); // site settings seeder

            write("Finished running seeds!");
        } else {
            write("Running only permission seed!");
            run("cd {{release_path}} && vendor/bin/phinx seed:run -s PermissionSeeder -c application/config/phinx.php"); // perm seeder
        }

        write("Succesfuly ran remote hosts migrations!");
    })->desc('Run migrations');

    /**
     * Main task
    */
    task('deploy', [
        'deploy:info',
        'deploy:prepare',
        'deploy:lock',
        'deploy:release',
        'deploy:update_code',
        'vendors',
        'upload_config',
        'upload_env',
        'generate_configs',
        'migrate',
        'deploy:shared',
        'deploy:symlink',
        'deploy:unlock',
        'cleanup'
    ])->desc('Deploy to server');

    // [Optional] if deploy fails automatically unlock.
    after('deploy:failed', 'deploy:unlock');
