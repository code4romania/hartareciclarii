<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubmitPointRequest;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\JsonResponse;

class SubmitPointController extends Controller
{
    public function __construct()
    {
        $this->middleware(HandlePrecognitiveRequests::class);
    }

    public function __invoke(SubmitPointRequest $request): JsonResponse
    {
        $attributes = $request->validated();

        return response()->json($attributes);
    }
}
