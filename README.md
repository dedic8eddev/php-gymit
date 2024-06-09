# GYMIT
Application for managing gyms.

## Konfigurace
vytvořit nový soubor **ci_app_config.json** a zkopírovat obsah z **ci_app_config.sample.json** ve složce `application/config/ci_app_config.json` a nakonfigurovat si **development** prostředí

Spouští se režim konfigurace podle toho v jakém režimu je nastavená samotná aplikace - **development | testing | production**

**! není možné spustit např. production konfiguraci pokud je aplikace nastavená v režimu development**

#### Spuštění konfigurace
```sh
$ php ci_config.php run
```
- Vygenerují se nové nadefinované soubory v konfiguraci. 
- Upravovat pouze soubory se označením **ci-sample-config**

**Pozor:** Vygenerované soubory manuálně neupravovat, při spuštění konfigurace se přepíší
Úprava generovaných konfiguračních souborů je pouze pro vlastní testovací účely

### Další funkce konfigurace

**Nastavení režimu aplikace**

Typ režimu doplnit **bez uvozovek**

```sh
$ php public/ci_config.php set_envrironment="development|testing|production"
```

**Vygenerování nového hlavního konfiguračního souboru**

Typ režimu doplnit **bez uvozovek**

```sh
$ php ci_config.php generate_config="development|testing|production"
```
Vygeneruje nový config soubor pouze s konfigurací zadaného řezimu
**Pozor:** přepíše původní **ci_app_config.json** soubor

## Migrace
**Spouštění migračních příkazů**

```sh
../vendor/bin/phinx [prikazy] -c application/config/phinx.php
```

[Dokumentace](http://docs.phinx.org/en/latest/index.html)

## Deployment (BETA)
**Deployment na server**

Config soubor **deploy/deploy.php**.

```sh
cd deploy
../vendor/bin/dep deploy production [ -vvv --ansi (debug) ]
```

[Dokumentace](https://deployer.org/)

## Running
```sh
$ cd gymit
$ composer (install / update)
$ phinx (migrate + seed)
$ php -S localhost:8000
```
## Starting microservices
```sh
$ cd microservices
$ cd transactions / etc
$ npm install
$ nodemon service.js
```

### Template

[Demo template available here](https://xvelopers.com/demos/html/paper-panel/index.html)