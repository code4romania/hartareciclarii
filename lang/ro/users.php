<?php

declare(strict_types=1);

return [
    'heading' => 'Creează / actualizează un utilizator Admin',
    'label' => 'Utilizatori',
    'reset_password' => [
        'subject' => 'Recuperare parolă',
        'email_heading' => 'Salut! Primesti acest email deoarece s-a efectuat o resetare a parolei pentru contul tau.',
        'link_label' => 'Resetează parola',
        'reset_time' => 'Link-ul de resetare a parolei va expira în ' . config('auth.passwords.users.expire') . ' minute',
    ],
    'group' => [
        'singular' => 'Grup utilizator',
        'plural' => 'Grupuri utilizator',
        'name' => 'Nume',
        'users_count' => 'Număr utilizatori',
    ],
    'role' => 'Rol',
    'email' => 'Email',
    'name' => 'Nume',
    'phone' => 'Telefon',
    'password' => 'Parolă',
    'roles' => 'Roluri',
    'points_count' => 'Număr puncte adǎugate',
    'issues_count' => 'Număr probleme adǎugate',
    'last_login_date' => 'Ultima datǎ de conectare',

];
