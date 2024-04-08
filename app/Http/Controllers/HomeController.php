<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\PointResource;
use App\Models\Point;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Home', [
            'points' => PointResource::collection(
                Point::query()
                    // ->inBounds($request->get('bounds', []))
                    ->get()
            ),
        ]);
    }
}
