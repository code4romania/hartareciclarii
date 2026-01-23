<?php

declare(strict_types=1);

return [

    'problem_status' => [
        'new' => 'Nerezolvată',
        'pending' => 'În rezolvare',
        'closed' => 'Rezolvată',
    ],

    'issue_status' => [
        'new' => 'Nou',
        'solved' => 'Rezolvat',
        'pending' => 'In asteptare',
        'denied' => 'Respins',
    ],

    'point_status' => [
        'verified' => 'Punct verificat',
        'unverified' => 'Necesita verificare',
        'problems' => 'Probleme raportate',
        'with_problems' => 'Punct cu probleme',
        'problems_count' => '{1} 1 problemă raportată|[2,19] :count probleme raportate|[20,*] :count de probleme raportate',
    ],
    'days' => [
        'monday' => 'Luni',
        'tuesday' => 'Marti',
        'wednesday' => 'Miercuri',
        'thursday' => 'Joi',
        'friday' => 'Vineri',
        'saturday' => 'Sambata',
        'sunday' => 'Duminica',
    ],
    'change_types' => [
        'administered_by' => 'Administrat de',

        'point_type' => 'Tip punct',
        'materials' => 'Materiale',
    ],
    'model_type' => [
        'place' => 'Loc',
        'point' => 'Punct',
        'problem' => 'Problema',
    ],
    'report_type' => [
        'points' => 'Puncte',
        'problems' => 'Probleme',
        'user_activity' => 'Activitate utilizator',
        'top_users' => 'Top contribuitori',
    ],
    'report_status' => [
        'pending' => 'In asteptare',
        'in_progress' => 'In curs de generare',
        'completed' => 'Generat',
    ],

    'issue' => [
        'default_issue_types' => [
            'default_incorrect_address' => 'Adresa nu este corectă',
            'default_incorrect_map_point' => 'Locația punctului pe hartă nu este corectă',
            'default_other' => 'Altă problemă',
        ],

        'repairs_issue_types' => [
            'repairs_issues_incorrect_address' => 'Adresa nu este corectă',
            'repairs_issues_incorrect_map_point' => 'Locația punctului pe hartă nu este corectă',
            'repairs_issues_incorrect_repaired_products' => 'Produsele reparate nu sunt corecte',
            'repairs_issues_incorrect_schedule' => 'Programul nu este corect',
            'repairs_issues_refused_repair' => 'Reparație refuzată',
            'repairs_issues_other' => 'Altă problemă',
        ],

        'fields' => [
            'address' => 'Adresă',
            'latitude' => 'Latitudine',
            'longitude' => 'Longitudine',
            'schedule' => 'Program',
            'container' => 'Container',
            'repaired_products_refused' => 'Produse refuzate pentru reparație',
            'repaired_products_not_repaired' => 'Produse care nu sunt reparate',
            'repaired_products_other' => 'Alte produse',
            'incorrect_materials_missing' => 'Materiale lipsă din listă',
            'incorrect_materials_extra' => 'Materiale în plus față de listă',
            'incorrect_materials_other' => 'Alte materiale',
            'refused_collection_type_of_material' => 'Tip de material refuzat',
            'refused_collection_quantity' => 'Cantitate refuzată',
            'refused_collection_other' => 'Alte motive de refuz',
        ],
    ],

    'issues' => [
        'waste_collection_issue_types' => [
            'waste_collection_incorrect_address' => 'Adresa nu este corectă',
            'waste_collection_incorrect_map_point' => 'Locația punctului pe hartă nu este corectă',
            'waste_collection_incorrect_materials' => 'Materialele nu sunt corecte',
            'waste_collection_incorrect_schedule' => 'Programul nu este corect',
            'waste_collection_malfunctioning_container' => 'Container defect',
            'waste_collection_refused_collection' => 'Colectare refuzată',
            'other' => 'Altă problemă',
        ],
    ],
];
