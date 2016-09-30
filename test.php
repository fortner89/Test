<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$locations = array(
	array('lat' => 38.446655, 'lon' => -82.299389),
	array('lat' => 38.136234, 'lon' => -92.912181),
	array('lat' => 38.830008, 'lon' => -94.518546)
	);
//REGEX by \u003eEntering Missouri\u003c
//Note: This might change once it's been decoded from JSON

//https://maps.googleapis.com/maps/api/directions/json?origin=38.446655,-82.299389&destination=38.830008,-94.518546&waypoints=38.136234,-92.912181&key=AIzaSyAlzwM5vJhI8yWYibpDzib4iyE0Tmx_7ik
/*echo "<pre>";
var_dump($locations);
echo "</pre>";*/

require_once ('classes/distanceCalculator.class.php');
$calc = new distanceCalculator('AIzaSyAlzwM5vJhI8yWYibpDzib4iyE0Tmx_7ik');

$res = $calc->getPoints(10, 10);
echo "<h1>Original</h1>";
echo "<pre>";
var_dump($res);
echo "</pre>";

$output = array_slice($res, 2, 5);
echo "<h1>Output</h1>";
echo "<pre>";
var_dump($output);
echo "</pre>";

echo "<h1>After</h1>";
echo "<pre>";
var_dump($res);
echo "</pre>";

/*echo "<pre>";
var_dump($calc->getDirections());
echo "</pre>";*/
//echo $calc->getState(38.446655, -82.299389);


//$calc->getState($locations[0]['lat'], $locations[0]['lon']);
