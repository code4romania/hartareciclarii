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
];
