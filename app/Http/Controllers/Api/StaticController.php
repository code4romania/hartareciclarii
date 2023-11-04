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
		$images =[];
		foreach ($request->file('images') as $image)
		{
			$images[] = $image->store('point_images');
		}
		
		
		return response()
			->json(
				[
					'images' => $images
				]);
	}
}
