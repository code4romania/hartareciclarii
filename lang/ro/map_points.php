<?php

declare(strict_types=1);

return [
    'id' => 'ID Punct',
    'point_type' => 'Tip',
    'point_type_alt' => 'Tip punct',
    'materials' => 'Materiale',
    'county' => 'Județul',
    'city' => 'Localitate',
    'address' => 'Adresa',
    'verified' => '* Verificat',
    'requires_verification' => '* Necesita verificare',
    'issues_found' => '* Probleme raportate',
    'title' => 'Puncte harta',
    'managed_by' => 'Administrat de',
    'group' => 'Grup',
    'location' => 'Localizare',
    'start_day' => 'Zi start',
    'end_day' => 'Zi sfarsit',
    'website' => 'Website',
    'email' => 'Email',
    'phone_no' => 'Telefon',
    'opening_time' => 'Ora deschidere',
    'closing_time' => 'Ora inchidere',
    'notes' => 'Notite',
    'offers_transport' => 'Ofera transport',
    'offers_money' => 'Ofera bani',
    'suggest_new_point' => 'Sugereaza un nou punct pe harta',
    'point_save_success' => 'Punct salvat cu succes',
    'point_save_success_body' => 'Pentru a vedea modificarile in pagina va rugam sa reincarcati pagina.',
    'import_materials' => 'Importa materiale',
    'use_default_values' => 'Foloseste valorile implicite',
    'proximity_points' => 'Puncte in apropiere care au accealeasi caracteristici',
    'proximity_count' => 'Numar puncte posibil duplicate',
    'distance' => 'Distanță',

    'subheading' => ':serviceType :pointType administrat de :administeredBy alocat la grup :group',

    'buttons' => [
        'set_group' => 'Aloca la grup',
        'location_type' => 'Tip locatie',
        'create' => 'Adauga punct nou',
        'import' => 'Importǎ puncte',
        'details' => 'Detalii punct',
        'delete' => 'Sterge punct',
        'view_on_map' => 'Vezi pe harta',
        'edit' => 'Modifica punct',
        'edit_details' => 'Modifica detalii',
        'validate' => 'Marcheaza ca verificat',
        'change_status_verified' => 'Seteaza statusul: verificat',
        'change_status_unverified' => 'Seteaza statusul: neverificat',
        'update_location' => 'Editeaza locatia',
        'confirm_info' => 'Confirma informatiile',
        'bulk_update' => 'Actualizeaza în masǎ',
    ],
    'sections' => [
        'location' => 'Localizare',
        'map' => 'Harta',
        'details' => 'Detalii',

    ],
    'fields' => [
        'address' => 'Adresǎ',
        'coordinate' => 'Coordonate',
        'notes' => 'Notițe localizare (private)',
        'service_type' => 'Tip serviciu',
        'status' => 'Status',
        'group' => 'Grup',
        'latitude' => 'Latitudine',
        'longitude' => 'Longitudine',
        'point_type' => 'Tip punct',
        'materials' => 'Materiale',
        'website' => 'Website',
        'phone' => 'Telefon',
        'email' => 'Email',
        'administered_by' => 'Administrat de',
        'day' => 'Zi',
        'opening_time' => 'Ora de deschidere',
        'closing_time' => 'Ora de inchidere',
        'schedule' => 'Program',
        'observations' => 'Observatii',
        'offers_transport' => 'Oferǎ transport',
        'offers_money' => 'Oferǎ bani',
        'offers_vouchers' => 'Ofera vouchere',
        'free_of_charge' => 'Oferă servicii gratuite',
        'images' => 'Imagini',
        'business_name' => 'Nume business',

    ],
    'notifications' => [
        'status_changed' => [
            'success' => 'Statusu actualizat cu succes',
            'warning' => 'Statusul nu a putut fi actualizat pentru :count puncte',
        ],
        'status_change_verified' => 'Statusul a fost setat verificat la toate punctele care nu au probleme raportate',
        'status_change_unverified' => 'Statusul a fost setat neverificat la toate punctele care nu au probleme raportate',

        'point_allocated_to_group' => 'Punctele (:number) au fost alocat cu succes la grupul :group',

        'point_added_body' => 'Punctul a fost adǎugat cu succes. :point',
    ],

    'wizard' => [
        'step_1' => 'Selecteaza tipul de locatie',
        'step_2' => 'Detalii punct',
        'step_3' => 'Materiale',
    ],

    'actions' => [
        'bulk_add_materials' => [
            'title' => 'Adaugǎ materiale',
            'success' => 'Materiale adǎugate cu succes la :count puncte',
            'error' => 'Materialele nu au putut fi adǎugate asigurate-te ca punctele selectate au tipul de serviciu corect',
        ],

        'bulk_remove_materials' => [
            'title' => 'Sterge materiale',
            'success' => 'Materiale sterse cu succes de la :count puncte',
            'error' => 'Materialele nu au putut fi sterse asigurate-te ca punctele au materialele selectate',
        ],
        'bulk_update_info' => [
            'title' => 'Actualizeaza informatiile',
            'field' => 'Câmp',
            'field_value' => 'Valoare câmp',
            'success' => 'Informațiile au fost actualizate cu succes pentru :count puncte',
            'error' => 'Informațiile nu au putut fi actualizate, te rugam sa incerci din nou mai tarziu, daca problema persista contacteaza echipa de suport',
        ],
    ],

    'new_point' => 'Punct nou',
    'edit_point' => 'Modificǎ punct',
    'new_address' => 'Adresa nouǎ:',
    'change_pin_location' => 'Schimbǎ locația',
    'change_address' => 'Schimbǎ adresa',
    'submit' => 'Adaugǎ punct',

];
