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
	
	public function getPoints($offset = 0, $limit = 25){
		$q = 'SELECT lat, lon FROM track_points ORDER BY id ASC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($q);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
	}
	
	public function getAllPoints(){
		$q = 'SELECT id, lat, lon FROM track_points ORDER BY id ASC';
        $stmt = $this->db->prepare($q);
        $stmt->execute();
        return $stmt->fetchAll();
	}

	public function getGeoCode($lat, $lon){
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&key='.$this->apiKey;
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($this->curl, CURLOPT_CAINFO, getcwd() . "/cer/google.cer");
		$result = curl_exec($this->curl);
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

	public function getDirections($coords){
		$dest = end($coords);
		if(count($coords) > 2){
			$waypoints = "&waypoints=";
			for ($i=1; $i < (count($coords) - 1); $i++) { 
				$waypoints .= $coords[$i]['lat'].",".$coords[$i]['lon']."|";
			}
			$waypoints = substr($waypoints, 0, -1);
		}
		else{
			$waypoints = "";
		}
		$url = 'https://maps.googleapis.com/maps/api/directions/json?origin='.$coords[0]['lat'].','.$coords[0]['lon'].'&destination='.$dest['lat'].','.$dest['lon'].$waypoints.'&key='.$this->key;
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($this->curl, CURLOPT_CAINFO, getcwd() . "/cer/google.cer");
		$result = curl_exec($this->curl);
		return json_decode($result, true);
	}

	public function calcStateDistance($coords){
		for ($i=0; $i < count($coords); $i++) { 
			# code...
		}
	}
}