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
		
		$judet = '';
		$localitate = '';
		if (!empty($address) && isset($address['address']))
		{
			if (isset($address['address']['county']))
			{
				$judet = $address['address']['county'];
			}
			else if (isset($address['address']['city_district']))
			{
				$judet = $address['address']['city'];
			}
			
			$county = County::where('name', $judet)->first();
			if ($county)
			{
				if (isset($address['address']['city']))
				{
					if (isset($address['address']['city_district']))
					{
						$localitate = 'Municipiul';
					}
					else
					{
						$localitate = $address['address']['city'];
					}
				}
				else if (isset($address['address']['town']))
				{
					$localitate = $address['address']['town'];
				}
				else if (isset($address['address']['village']))
				{
					$localitate = $address['address']['village'];
				}
				else if (isset($address['address']['municipality']))
				{
					$localitate = $address['address']['municipality'];
				}
				
				$city = City::where('name', $localitate)->where('county_id', $county->id)->first();
			}
			
			return [
				'lat' => $address['lat'],
				'lon' => $address['lon'],
				'display_name' => $address['display_name'],
				'city' => $localitate,
				'county' => $judet,
				'county_id' => isset($county) ? $county->id : 0,
				'city_id' => isset($city) ? $city->id: 0,
				'postcode' => $address['address']['postcode']
			];
		}
		
		return [];
	}
}
