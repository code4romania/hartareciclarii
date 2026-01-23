<?php

return [

    'label' => 'Navigare',

    'overview' => '{1} Afișare 1 rezultat|[2,*] Afișare :first-:last din :total rezultate',

    'fields' => [

        'records_per_page' => [

            'label' => 'Pe pagină',

            'options' => [
                'all' => 'Toate',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Mergi la pagina :page',
        ],

        'next' => [
            'label' => 'Pagina următoare',
        ],

        'previous' => [
            'label' => 'Pagina precedentă',
        ],

    ],

];
