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

	public function getState($lat, $lon, $name = 'long_name'){
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
		$url = 'https://maps.googleapis.com/maps/api/directions/json?origin='.$coords[0]['lat'].','.$coords[0]['lon'].'&destination='.$dest['lat'].','.$dest['lon'].$waypoints.'&key='.$this->apiKey;
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($this->curl, CURLOPT_CAINFO, getcwd() . "/cer/google.cer");
		$result = curl_exec($this->curl);
		return json_decode($result, true);
	}

	public function calcStateDistance($coords){
		$totals = array();
		$maxPoints = count($coords);
		$maxW = 24;
		$nextStart = $coords[0];
		$currentState = $this->getState($nextStart["lat"], $nextStart["lon"]);
		for ($i=1; $i < $maxPoints; $i+=$maxW) { 
			$output = array_slice($coords, $i, $maxW);
			array_unshift($output, $nextStart);
			$rData = $this->getDirections($output);
			for ($y=0; $y < count($rData["routes"][0]["legs"]); $y++) { 
				for ($z=0; $z < count($rData["routes"][0]["legs"][$y]["steps"]); $z++) { 
					if(isset($totals[$currentState]['distance'])){
						$totals[$currentState]['distance'] += $rData["routes"][0]["legs"][$y]["steps"][$z]["distance"]["value"];	
					}
					else{
						$totals[$currentState]['distance'] = $rData["routes"][0]["legs"][$y]["steps"][$z]["distance"]["value"];
					}
					
					preg_match('(Entering ([\w ]+))', $rData["routes"][0]["legs"][$y]["steps"][$z]["html_instructions"], $matches);
					//This contains the state as a long name
					if(isset($matches[1])){
						echo "<h1>State Change</h1>";
						echo "<pre>";
						var_dump($matches);
						echo "</pre>";
						echo $rData["routes"][0]["legs"][$y]["steps"][$z]["html_instructions"];
						$currentState = $matches[1];
					}
					
				}
				
			}
			$nextStart = end($output);
		}
		echo "<pre>";
		var_dump($totals);
		echo "</pre>";
	}
}