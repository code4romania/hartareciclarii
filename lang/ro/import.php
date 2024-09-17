<?php

declare(strict_types=1);

return [

    'singular' => 'Import',
    'plural' => 'Importuri',
    'columns' => [
        'file' => 'Fisier',
        'user' => 'Utilizator',
        'created_at' => 'Creat la',
        'started_at' => 'Inceput la',
        'completed_at' => 'Finalizat la',
        'processed' => 'Adaugate',
        'failed' => 'Abandonate',
        'duration' => 'Durata',
        'status' => 'Status',

    ],
    'status' => [
        'pending' => 'In asteptare',
        'processing' => 'In procesare',
        'view' => 'Vezi raport',
    ],

    'filters' => [
        'is_completed' => 'Finalizat',
        'with_errors' => 'Cu erori',
    ],
    'imported_points' => [
        'title' => 'Importate cu succes (:number)',
        'description' => 'Următoarele puncte au fost adăugate cu succes în baza de date și au fost afișate pe hartă.',
    ],
    'details' => 'Detalii',
    'failed' => [
        'title' => 'Puncte abandonate',
        'description' => 'Următoarele puncte nu au fost adăugate la baza de date de puncte de pe hartă pentru că au probleme legate de formatul coordonatelor sau au fost identificate cu risc crescut de duplicat. Vă rugăm verificați-le și reîncercați să le adăugați individual sau prin reluarea unui proces de import.',
        'no_failed' => 'Nu există puncte abandonate.',
    ],
    'view_heading' => 'Importul #:id',

    'view_subheading' => 'Importat de :user la :created_at. Finalizat la :completed_at.',

];
