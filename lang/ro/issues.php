<?php

/**
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-31 09:27:44
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-11-27 07:59:35
 */
return [
    'status_updated' => 'Status salvat',
    'header'=>[
        'list' => 'Lista probleme raportate',
    ],
    'columns' => [
        'reporter' => 'Utilizator',
        'created_at' => 'Raportat la',
        'issue_type' => 'Tip problema',
        'status' =>'Status problema',
        'map_point_id' => 'ID punct',
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

];
