<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MapPointServiceResource;
use App\Models\MapPointService;
use Illuminate\Http\Request;

class StaticController extends Controller
{
    public function filters(Request $request)
    {
        $service_id = $request->get('service_id', 0);

        return response()
            ->json(
                [
                    'filters' => MapPointServiceResource::collection(MapPointService::all()),
                    'extended_filters' => MapPointService::getExtendedFilters((int) $service_id),
                ]
            );
    }

    public function upload(Request $request)
    {
        $this->validate($request, [
            'images' => [
                'required',
                'array',
                'max:10',
            ],
            'images.*' => [
                'required',
                'image',
            ],
        ]);
        $images = [];
        foreach ($request->file('images') as $image) {
            $images[] = $image->store('point_images');
        }

        return response()
            ->json(
                [
                    'images' => $images,
                ]
            );
    }

    public function recapcha(Request $request)
    {
        $this->validate($request, [
            'recaptchaToken' => recaptchaRuleName(),
        ]);

        return response()
            ->json(['ok' => 'ok']);
    }
}
