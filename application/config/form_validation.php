<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Form validation config
*/

$config['validation_login'] = [
    [
        'field' => 'email',
        'label' => 'email',
        'rules' => 'required',
    ],
    [
        'field' => 'password',
        'label' => 'Heslo',
        'rules' => 'required',
    ],
];

$config['front_user_personal_info'] = [
    [
      'field' => 'first_name',
      'label' => 'Jméno',
      'rules' => 'required|alpha',
    ],
    [
        'field' => 'last_name',
        'label' => 'Příjmení',
        'rules' => 'required|alpha',
    ],
    [
        'field' => 'birth_date',
        'label' => 'Datum narození',
        //'rules' => 'required',
    ],
    [
        'field' => 'email',
        'label' => 'E-mail',
        //'rules' => 'required',
    ],
    [
        'field' => 'phone',
        'label' => 'Příjmení',
        //'rules' => 'Telefon',
    ],
    [
        'field' => 'street',
        'label' => 'Ulice',
        //'rules' => 'required',
    ],
    [
        'field' => 'city',
        'label' => 'Město',
        //'rules' => 'required',
    ],
    [
        'field' => 'zip',
        'label' => 'PSČ',
        //'rules' => 'required',
    ],
    [
        'field' => 'country',
        'label' => 'Stát',
        //'rules' => 'required',
    ],
];

$config['front_user_security_info'] = [
    [
        'field' => 'username',
        'label' => 'Přihlašovací E-mail',
        'rules' => 'required|valid_email',
        'errors' => [
            'required' => '%s je povinný',
            'valid_email' => '%s musí být platná e-mailová adresa',
        ],
    ],
    [
        'field' => 'current_password',
        'label' => 'Aktuální heslo',
    ],
    [
        'field' => 'new_password',
        'label' => 'Nové heslo',
    ],
];

$config['front_validation_user_security_info'] = [
    [
        'field' => 'email',
        'label' => 'email',
        'rules' => 'required|valid_email',
    ],
    [
        'field' => 'password',
        'label' => 'Heslo',
        'rules' => 'required',
    ],
];