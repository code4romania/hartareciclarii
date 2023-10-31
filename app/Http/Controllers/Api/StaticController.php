<?php

namespace App\Http\Controllers\Api;

use App\Models\MapPointService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaticController extends Controller
{
	public function filters(Request $request)
	{
		$service_id = $request->get('service_id', 0);
		return response()
			->json(
			[
				'filters' => MapPointService::all(),
				'extended_filters' => MapPointService::getExtendedFilters((int) $service_id),
			]);
	}
	
	public function upload(Request $request)
	{
		$this->validate($request, [
			'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
		]);
		$image_path = $request->file('image');
		$file = \hash('sha256', $image_path->getClientOriginalName().time()).'.'.pathinfo($image_path->getClientOriginalName(), PATHINFO_EXTENSION);
		
		$image = $image_path->store('point_images');
		
		return response()
			->json(
				[
					'image' => $image
				]);
	}
}
