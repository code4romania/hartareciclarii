<?php

declare(strict_types=1);

return [
    'heading' => 'Create / update an admin user',
    'label' => 'Users',
    'reset_password' => [
        'subject' => 'Recuperare parola',
        'email_heading' => 'Salut! Primesti acest email deoarece s-a efectuat o resetare a parolei pentru contul tau.',
        'link_label' => 'Reseteaza parola',
        'reset_time' => 'Link-ul de resetare a parolei va expira in ' . config('auth.passwords.users.expire') . ' minute',
    ],
    'group' => [
        'singular' => 'Grup utilizator',
        'plural' => 'Grupuri utilizator',
        'name' => 'Nume',
        'users_count' => 'Numar utilizatori',
    ],
    'role' => 'Rol',
    'email' => 'Email',
    'name' => 'Nume',
    'phone' => 'Telefon',
    'password' => 'Parola',
    'roles' => 'Roluri',
    'points_count' => 'Numar puncte adǎugate',
    'issues_count' => 'Numar probleme adǎugate',
    'last_login_date' => 'Ultima datǎ de conectare',

];
