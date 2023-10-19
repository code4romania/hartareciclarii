<?php

namespace App\Http\Controllers\Api;

use App\Models\MapPoint;
use App\Models\MapPointService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MapController extends Controller
{
	public function points(Request $request)
	{
		$filters = $request->get('filters', []);
		return response()
			->json(
			[
				'points' => MapPoint::getFilteredMapPoints($filters)
			]);
	}
}
