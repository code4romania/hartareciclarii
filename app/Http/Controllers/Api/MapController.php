<?php

namespace App\Http\Controllers\Api;

use App\Models\MapPoint;
use App\Models\MapPointField;
use App\Models\MapPointService;
use App\Models\RecycleMaterial;
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
	
	public function create(Request $request)
	{
		$data = $request->all();
		return response()
			->json($data);
	}
	
	public function nomenclatures(Request $request)
	{
		return response()
			->json(
				[
					'services' => MapPointService::with('pointTypes')->get(),
					'material_recycling_points' => RecycleMaterial::whereNull('is_wildcard')->get(),
					'field_types' => MapPointField::all(),
				]);
	}
}
