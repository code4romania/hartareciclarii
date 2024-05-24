<?php

namespace App\Http\Controllers;

use App\Http\Resources\PointResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function dashboard()
    {

        return Inertia::render(
            'Dashboard',
            [
                'contributions' => [
                    [
                        'id' =>1,
                        'type' => "Adaugare punct nou",
                        'point_type' =>"Punct colectare selectivă deșeuri (Container stradal)",
                        'location' => "Strada Mihai Eminescu, nr. 1, București",
                        'date' => "2021-09-01 12:00:00",
                    ]
                ],
            ]
        );
    }
}
