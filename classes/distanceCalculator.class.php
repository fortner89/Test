<?php

require_once('connect.php');

//This class is used to calculate distances in states and determine locations
class distanceCalculator {
	private $db;
	//Google Maps API key
	public $apiKey;
	private $curl;

	function __construct($apiKey = ""){
		$this->curl = curl_init();
		$this->apiKey = $apiKey;
		$this->db = connect();
	}
	
	//Retrieves coordinates from the track_points table
	public function getPoints($offset = 0, $limit = 25){
		$q = 'SELECT lat, lon FROM track_points ORDER BY id ASC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($q);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
	}
	
	//Retrieves all points from the track_points table
	public function getAllPoints(){
		$q = 'SELECT id, lat, lon FROM track_points ORDER BY id ASC';
        $stmt = $this->db->prepare($q);
        $stmt->execute();
        return $stmt->fetchAll();
	}

	//This retrieves geocode information
	public function getGeoCode($lat, $lon){
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&key='.$this->apiKey;
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($this->curl, CURLOPT_CAINFO, getcwd() . "/cer/google.cer");
		$result = curl_exec($this->curl);
		return json_decode($result, true);
	}

	//This retrieves what state a specific set of coords are in. $name accepts long_name "California" and short_name "CA"
	public function getState($lat, $lon, $name = 'long_name'){
		$result = $this->getGeoCode($lat, $lon);
		//This drills down through the results looking for administrative_area_level_1. 
		//This is where the state information can be found.
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

	//This will provide the route information based on an array of coords that is returned from the DB
	private function getDirections($coords){
		//The api requires a start, end, and waypoint destinations
		//Max number of coordinates is 25
		//Destination coords
		$dest = end($coords);

		//This checks to see if there are more than 2 sets of coordinates
		//If there are, then additional coordinates will need to be added as waypoints
		if(count($coords) > 2){
			$waypoints = "&waypoints=";
			for ($i=1; $i < (count($coords) - 1); $i++) { 
				$waypoints .= $coords[$i]['lat'].",".$coords[$i]['lon']."|";
			}
			//This leftover "|"
			$waypoints = substr($waypoints, 0, -1);
		}
		//If there are only two coordinates, then it won't add waypoint information to the query
		else{
			$waypoints = "";
		}

		//Query is built here
		$url = 'https://maps.googleapis.com/maps/api/directions/json?origin='.$coords[0]['lat'].','.$coords[0]['lon'].'&destination='.$dest['lat'].','.$dest['lon'].$waypoints.'&key='.$this->apiKey;
		
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		//Had to add these two options along with the .cer to allow cURLing to https URL
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($this->curl, CURLOPT_CAINFO, getcwd() . "/cer/google.cer");
		
		$result = curl_exec($this->curl);

		return json_decode($result, true);
	}

	//This function is used to calculate the total distance a truck travels in a state
	//The first paramter supplied should be retrieved from the database with using either getPoints or getAllPoints
	//Options for unit are "mi" and "km"
	//Options for $displayResult are 0 (return only the data array) and 1 (echo a table containing the info as well)
	public function calcStateDistance($coords, $unit = "mi", $displayResult = 0){
		//Perform validation on additional paramters
		if($unit != "mi" && $unit != "km"){
			$errors[] = "Invalid unit. Please use km or mi.";
		}
		if($displayResult != 0 && $displayResult != 1){
			$errors[] = "Invalid displayResult paramter. Please use 0 or 1.";
		}
		if(isset($errors)){
			return $errors;
		}

		//This contains the total distance traveled for each state
		$totals = array();
		//This determines how many total sets of coordinates are being delt with
		$maxPoints = count($coords);
		//This is how many elements are allowed in query to the Directions API 25 total is max.
		//24 is the max number for this variable because of the way the data is manipulated.
		$maxW = 24;
		//This variable is used to remember where the destination coordinates were from the previous query
		//These are then used as origin coordinates for the query the following query
		$nextStart = $coords[0];
		//This variable is used to collect denote what state is currently being measured.
		//It's value is used as a key in the array that keeps the totals
		$currentState = $this->getState($nextStart["lat"], $nextStart["lon"]);

		//Loop through all the coordinates $maxW at a time and continuing to jump by $maxW. 
		//Start at one because $nextStart contains [0]
		for ($i=1; $i < $maxPoints; $i+=$maxW) { 
			//This grabs a $maxW sized portion of the total coordinates being delt with. Offset by $i 
			$output = array_slice($coords, $i, $maxW);
			//This places $nextStart at [0] of the array
			array_unshift($output, $nextStart);
			//Uses the set of coords stored in $output to get direction information
			$rData = $this->getDirections($output);

			//This loops through legs to loop through steps which contains the relevant information
			for ($y=0; $y < count($rData["routes"][0]["legs"]); $y++) { 
				for ($z=0; $z < count($rData["routes"][0]["legs"][$y]["steps"]); $z++) { 
					//If statement to intitialize index before adding
					//$currentState being used as the index to store/add the distance at
					if(isset($totals[$currentState])){
						$totals[$currentState] += $rData["routes"][0]["legs"][$y]["steps"][$z]["distance"]["value"];	
					}
					else{
						$totals[$currentState] = $rData["routes"][0]["legs"][$y]["steps"][$z]["distance"]["value"];
					}
					//Pulling the state information from html_instructions
					preg_match('(Entering ([\w ]+))', $rData["routes"][0]["legs"][$y]["steps"][$z]["html_instructions"], $matches);
					//If this is set, that means another state has been entered.
					if(isset($matches[1])){
						$currentState = $matches[1];
					}
				}
			}
			//This sets the origin coords for the next query using the destination of this one.
			$nextStart = end($output);
		}

		//Sets the unit type
		if($unit == "mi"){
			$totals = $this->metersToMiles($totals);
		}
		elseif ($unit == "km") {
			$totals = $this->metersToKilometers($totals);
		}
		
		//Echos formatted result if requested
		if($displayResult === 1){
			echo $this->displayStateDistance($totals, $unit);
		}
		
		//Returns array of totals
		return $totals;
	}

	//Converts meters to miles from the $totals array
	private function metersToMiles($data){
		foreach ($data as $key => $value) {
			$data[$key] = round($value / 1609.344, 2);
		}
		return $data;
	}

	//Converts meters to kilometers from the $totals array
	private function metersToKilometers($data){
		foreach ($data as $key => $value) {
			$data[$key] = round($value / 1000, 2);
		}
		return $data;
	}

	//This is used to display formatted results for calcStateDistance
	//Options for $unit are "mi" and "km".
	private function displayStateDistance($data, $unit = "mi"){
		switch ($unit) {
			case 'mi':
				$unit = " miles";
				break;
			case 'km':
				$unit = " kilometers";
				break;
			default:
				return "Invalid unit paramter";
				break;
		}
		$html = "
		<table>
			<thead>
				<tr>
					<th>State</th>
					<th>Distance (".$unit.")</th>
				</tr>
			</thead>
			<tbody";
		foreach ($data as $key => $value) {
			$html .= "
				<tr>
					<td>".$key."</td>
					<td>".$value."</td>
				</tr>";
		}
		$html .= "
			</tbody>
		</table>";
		
		return $html;
	}
}