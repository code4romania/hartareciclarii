<?php

/**
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-23 10:36:09
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-31 12:28:58
 */
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
        'range_end'=> 'Adaugat sfarsind cu',
        'title' => 'Titlu raport',
        'created_at' => 'Creat',
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
        'county' => 'Judet',
        'city' => 'Localitate',
        'user_type' => 'Tip utilizator',
        'issue_type' => 'Tip problema',
    ],
    'action' => [
        'generate'=> 'Genereaza raport',
        'reset'=>'Reseteaza raportul',
        'save_report' => 'Salveaza raportul',
        'export' => 'Exporta',
        'saved' => 'Raport salvat',
    ],
    'section'=>[
        'generator' => 'Genereaza raport',
        'filter_dates_by' => 'Filtreaza datele dupa: ',
        'group_info_by' => 'Grupeaza informatiile in tabel dupa: ',
        'filter_date_range' => 'Perioada',
    ],
    'header'=>[
        'list' => 'Lista rapoarte',
    ],
    'placeholder' => [
        'select_one' => 'Selecteaza o optiune',
    ],
    'tabs' => [
        'map_points' => 'Puncte',
        'issues' => 'Probleme raportate',
    ],
];
