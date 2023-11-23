<?php

/*
 * @Author: bib
 * @Date:   2023-10-03 10:55:55
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-10 13:13:54
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Geolocation extends Model
{
    protected $table = 'counties';
	
	public static function reverse($lat, $lng)
	{
		$client = new \GuzzleHttp\Client();
		$uri = config('services.nominatim.url') . '/reverse?format=json&lat=' . $lat . '&lon=' . $lng . '&zoom=18&addressdetails=1';
		$response = $client->request('GET', $uri);
		
		$address = [];
		try
		{
			$contents = $response->getBody()->getContents();
			$address = json_decode($contents, true);
		}
		catch (\Exception $e)
		{
			return [];
		}
		
		if (!empty($address) && isset($address['address']))
		{
			$county = County::where('name', $address['address']['county'])->first();
			if ($county)
			{
				$city = City::where('name', $address['address']['city'])->where('county_id', $county->id)->first();
			}
			
			return [
				'lat' => $address['lat'],
				'lon' => $address['lon'],
				'display_name' => $address['display_name'],
				'city' => $address['address']['city'],
				'county' => $address['address']['county'],
				'county_id' => ($county) ? $county->id : 0,
				'city_id' => ($city) ? $city->id: 0,
				'postcode' => $address['address']['postcode']
			];
		}
		
		return [];
	}
}
