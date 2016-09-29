<?php

require_once('connect.php');

class distanceCalculator {
	private $db;
	public $apiKey;
	private $curl;
	function __construct($apiKey = ""){
		$this->curl = curl_init();
		$this->apiKey = $apiKey;
		$this->db = connect();
	}
	
	public function getPoints($offset = 0, $limit = 25, $order = "asc"){
		$q = 'SELECT CONCAT(\'"\',lat,", ",lon,\'"\') as coords FROM track_points LIMIT 10';
		$stmt = $this->db->prepare($q);
		$stmt->execute();
		$data = $stmt->fetchAll();
		$dataStr = "";
		for ($i=0; $i < count($data); $i++) { 
			$dataStr .= $data[$i]['coords'].",";
		}
		$dataStr = substr_replace($dataStr, "", -1);
		/*echo $dataStr;*/
		return $dataStr;
	}

	public function getGeoCode($lat, $lon){
		$curl = curl_init();
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&key='.$this->apiKey;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_CAINFO, getcwd() . "/cer/google.cer");
		$result = curl_exec($curl);
		return json_decode($result, true);
	}

	public function getState($lat, $lon, $name = 'short_name'){
		$result = $this->getGeoCode($lat, $lon);
		foreach ($result['results'][0]['address_components'] as $key => $value) {
			if(isset($result['results'][0]['address_components'][$key]['types'][0]) && $result['results'][0]['address_components'][$key]['types'][0] == "administrative_area_level_1"){
				switch ($name){
					case 'short_name': 
						return $result['results'][0]['address_components'][$key]['short_name'];
						break;
					case 'long_name': 
						return $result['results'][0]['address_components'][$key]['long_name'];
						break;
					default: 
						return 'Invalid Name';
						break;
				}
				
			}
		}
	}
}