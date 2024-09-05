<?php

declare(strict_types=1);

return [
    'label' => 'Probleme raportate',
    'status_updated' => 'Status salvat',
    'header' => [
        'list' => 'Listă probleme raportate',
        'view' => 'Probleme cu punctul  #:point_id',
    ],
    'subheader' => [
        'view' => 'Raportată de :user la :created_at',
    ],
    'columns' => [
        'reporter' => 'Utilizator',
        'created_at' => 'Raportat la',
        'issue_type' => 'Tip problemă',
        'service_type' => 'Tip serviciu',
        'status' => 'Status problemă',
        'map_point_id' => 'ID punct',
        'no_user' => 'Utilizator necunoscut',
        'created_from' => 'Creat de la',
        'created_until' => 'Creat până la',
    ],

    'infolistFields' => [
        'issue_type' => 'Ce tip de problemă ai identificat?',
        'description' => 'Descrie problema',
    ],

    'status' => [
        '0' => 'Neverificată',
        '1' => 'Rezolvată',
        '2' => 'În verificare',
        '3' => 'Rezolvată',
    ],
    'buttons' => [
        'details' => 'Detalii',
        'update_status' => 'Modifică status',
        'view_map_point' => 'Vezi punct',
    ],
    'labels' => [
        'issue' => 'Sesizată ',
        'reported_by' => 'de :name ',
        'reported_at' => 'la :created_at ',
        'issue_with_point' => 'Problema cu punctul #:point_id',
        'address_is_not_correct' => 'Sugestie adresă',
        'issue_type' => 'Tip problemă',
        'material_issue_missing' => 'Care dintre următoarele materiale ați identificat că <strong>lipsesc</strong> din descrierea locației?',
        'material_issue_extra' => 'Care dintre următoarele materiale ați identificat că se colectează <strong>în plus</strong> față de descrierea locației?',
        'opening_hours' => 'Orar',
        'description' => 'Descriere problemă',
        'decline_reason' => 'Motivul refuzului',
    ],

    'notifications' => [
        'status_changed' => 'Statusul problemei a fost modificat',
    ],
    'actions' => [
        'delete' => 'Șterge problema',
        'edit_point' => 'Editează punct',
        'change_status' => 'Schimbă status',
    ],
    'filters' => [
        'period' => 'Perioada: ',
    ],

];
