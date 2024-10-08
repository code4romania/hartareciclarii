<?php

declare(strict_types=1);

return [
    'label' => 'Probleme raportate',
    'status_updated' => 'Status salvat',
    'header' => [
        'list' => 'Lista probleme raportate',
        'view' => 'Probleme cu punctul  #:point_id',
    ],
    'subheader' => [
        'view' => 'Raportata de :user la :created_at',
    ],
    'columns' => [
        'reporter' => 'Utilizator',
        'created_at' => 'Raportat la',
        'issue_type' => 'Ce tip de problemă ai identificat?',
        'selected_subtypes' => 'Probleme identificate',
        'current_location' => 'Locatie curentǎ',
        'new_location' => 'Locație noua',
        'current_address' => 'Adresa curenta',
        'new_address' => 'Adresa noua',
        'collected_materials' => 'Materiale colectate',
        'collected_materials_hint' => 'Materiale colectate dar care lipsesc din lista',
        'not_collected_materials' => 'Materiale ne-colectate',
        'not_collected_materials_hint' => 'Materiale care nu sunt colectate dar sunt  in lista',
        'description' => 'Descrie problema',
        'service_type' => 'Tip serviciu',
        'status' => 'Status problema',
        'map_point_id' => 'ID punct',
        'no_user' => 'Utilizator necunoscut',
        'created_from' => 'Creat de la',
        'created_until' => 'Creat pana la',
    ],

    'infolistFields' => [
        'issue_type' => 'Ce tip de problemă ai identificat?',
        'description' => 'Descrie problema',
    ],

    'status' => [
        '0' => 'Neverificata',
        '1' => 'Rezolvata',
        '2' => 'In verificare',
        '3' => 'Rezolvata',
    ],
    'buttons' => [
        'details' => 'Detalii',
        'update_status' => 'Modifica status',
        'view_map_point' => 'Vezi punct',
    ],
    'labels' => [
        'issue' => 'Sesizata ',
        'reported_by' => 'de :name ',
        'reported_at' => 'la :created_at ',
        'issue_with_point' => 'Problema cu punctul #:point_id',
        'address_is_not_correct' => 'Sugestie adresa',
        'issue_type' => 'Tip problema',
        'material_issue_missing' => 'Care din urmatoarele materiale ati identificat ca <strong>lipsesc</strong> din descrierea locatiei?',
        'material_issue_extra' => 'Care din urmatoarele materiale ati identificat ca se colecteaza <strong>in plus</strong> fata de descrierea locatiei?',
        'opening_hours' => 'Orar',
        'description' => 'Descriere problema',
        'decline_reason' => 'Motivul refuzului',
    ],

    'notifications' => [
        'status_changed' => 'Statusul problemei a fost modificat',
    ],
    'actions' => [
        'delete' => 'Şterge problema',
        'edit_point' => 'Editeaza punct',
        'change_status' => 'Schimba status',
    ],
    'filters' => [
        'period' => 'Perioada: :created_from - :created_until',
    ],

];
