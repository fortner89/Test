<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once ('classes/distanceCalculator.class.php');
$calc = new distanceCalculator('AIzaSyAlzwM5vJhI8yWYibpDzib4iyE0Tmx_7ik');
echo $calc->getState(38.446655, -82.299389);
/*$locations = $calc->getPoints();
$locations = array(
	'"38.446655, -82.299389"',
	'"38.136234, -92.912181"',
	'"38.830008, -94.518546"'
	);
$locations = implode(",", $locations);*/
/*$curl = curl_init();
$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key=AIzaSyAlzwM5vJhI8yWYibpDzib4iyE0Tmx_7ik';
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curl, CURLOPT_CAINFO, getcwd() . "/cer/google.cer");*/

/*curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);*/
/*$result = curl_exec($curl);
$result = json_decode($result, true);*/
//$result = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=AIzaSyAlzwM5vJhI8yWYibpDzib4iyE0Tmx_7ik');
//echo $result['results'][0]['address_components'][4]['short_name'];
/*echo "<pre>";
var_dump($result);
echo "</pre>";*/
/*foreach ($result['results'][0]['address_components'] as $key => $value) {
	if(isset($result['results'][0]['address_components'][$key]['types'][0]) && $result['results'][0]['address_components'][$key]['types'][0] == "administrative_area_level_1"){
		echo $result['results'][0]['address_components'][$key]['short_name'];
	}
}*/

$instructions = array();
$distance = array();
$state = "start";
$currentState = "";




/*echo "<pre>";
var_dump($result);
echo "</pre>";*/
/*for ($i=0; $i < count($result['renderAdvancedNarrative']['route']['legs']); $i++) { 
	for ($y=0; $y < count($result['renderAdvancedNarrative']['route']['legs'][$i]['maneuvers']); $y++) { 
		$instructions[]['narrative'] = $result['renderAdvancedNarrative']['route']['legs'][$i]['maneuvers'][$y]['narrative'];
		//$instructions[]['distance'] = $result['renderAdvancedNarrative']['route']['legs'][$i]['maneuvers'][$y]['distance'];
		//$distance[$state]
	}
}*/

/*echo "<textarea>";
var_dump($instructions);
echo "</textarea>";*/
