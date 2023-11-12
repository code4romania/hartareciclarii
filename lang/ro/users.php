<?php

return [
    'heading' => 'Create / update an admin user',
    'label' => 'Users',
	'reset_password' => [
		'subject' => 'Recuperare parola',
		'email_heading' => "Salut! Primesti acest email deoarece s-a efectuat o resetare a parolei pentru contul tau.",
		'link_label' => 'Reseteaza parola',
		'reset_time' => 'Link-ul de resetare a parolei va expira in '.config('auth.passwords.users.expire').' minute'
	]
];
