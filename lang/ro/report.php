<?php

declare(strict_types=1);

return [
    'column' => [
        'service_type' => 'Tip serviciu',
        'point_type' => 'Tip punct',
        'materials' => 'Materiale (pentru CSID)',
        'location' => 'Locatie',
        'admin' => 'Administratori',
        'status' => 'Status',
        'date_from' => 'Adaugat in perioada',
        'fields' => 'Caracteristici',
        'city' => 'Oras',
        'county' => 'Judet',
        'range_start' => 'Adaugat incepand cu',
        'range_end' => 'Adaugat sfarsind cu',
        'title' => 'Titlu raport',
        'created_at' => 'Creat',
        'range' => 'Perioada',
        'status_options' => [
            -1 => 'Orice status',
            0 => 'Necesita verifcare',
            1 => 'Verificat',
        ],
        'issues_options' => [
            -1 => 'Orice status',

        ],
        'users_type' => [
            -1 => 'Orice utilizator',
            0 => 'Autentificat',
            1 => 'Neautentificat',
        ],
        'activities_options' => [
            -1 => 'Toate evenimentele',
            0 => 'Adaugare punct',
            1 => 'Raportare problema',
        ],
        'county' => 'Judet',
        'city' => 'Localitate',
        'user_type' => 'Tip utilizator',
        'issue_type' => 'Tip problema',
        'activity_type' => 'Tip contributie',
        'issue_status' => 'Status problema',
        'points_added' => 'Numar puncte adaugate',
        'issues_added' => 'Numar probleme adaugate',
        'over_100' => 'Peste 100',

    ],
    'action' => [
        'generate' => 'Genereaza raport',
        'reset' => 'Reseteaza raportul',
        'save_report' => 'Salveaza raportul',
        'export' => 'Exporta',
        'saved' => 'Raport salvat',
    ],
    'section' => [
        'generator' => 'Genereaza raport',
        'filter_dates_by' => 'Filtreaza datele dupa: ',
        'group_info_by' => 'Grupeaza informatiile in tabel dupa: ',
        'filter_date_range' => 'Perioada',
    ],
    'header' => [
        'list' => 'Lista rapoarte',
    ],
    'placeholder' => [
        'select_one' => 'Selecteaza o optiune',
    ],
    'tabs' => [
        'map_points' => 'Puncte',
        'issues' => 'Probleme raportate',
        'users' => 'Activitate utilizatori',
    ],
    'labels' => [
        'grouped_by' => 'Grupat: ',
        'generated_at' => 'Generat la: ',
    ],
];
