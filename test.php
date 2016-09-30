<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*$locations = array(
	array('lat' => 38.446655, 'lon' => -82.299389),
	array('lat' => 38.136234, 'lon' => -92.912181),
	array('lat' => 35.485772, 'lon' => -80.011138)
	);
//Charleston to Ohio
$locations = array(
	array('lat' => 38.317251, 'lon' => -81.561116),
	array('lat' => 38.396444, 'lon' => -82.411767),
	array('lat' => 38.416034, 'lon' => -82.486232),
	array('lat' => 38.433376, 'lon' => -82.580937)
	);

38.317251, -81.561116
38.396444, -82.411767
38.416034, -82.486232
//This is a point in Ohio
38.433376, -82.580937
*/

//First three are supposed to total 61.5 miles (61.477)
//Last two are supposed to total 6.5 miles
//All four are supposed to total 68.0 miles
//Goes is supposed to go down to 67.1 miles when plotted from with just first and last points

/*$locations = array(
	array('lat' => 38.317251, 'lon' => -81.561116),
	array('lat' => 38.365298, 'lon' => -82.769247),
	array('lat' => 38.540206, 'lon' => -82.678531),
	);*/



//REGEX by \u003eEntering Missouri\u003c
//Note: This might change once it's been decoded from JSON

//https://maps.googleapis.com/maps/api/directions/json?origin=38.446655,-82.299389&destination=38.830008,-94.518546&waypoints=38.136234,-92.912181&key=AIzaSyAlzwM5vJhI8yWYibpDzib4iyE0Tmx_7ik
/*echo "<pre>";
var_dump($locations);
echo "</pre>";*/

require_once ('classes/distanceCalculator.class.php');
$calc = new distanceCalculator('AIzaSyAlzwM5vJhI8yWYibpDzib4iyE0Tmx_7ik');

//$res = $calc->getPoints(10, 10);
$res = $calc->getPoints(0, 202);

$calc->calcStateDistance($res);


//$arr["routes"]["legs"]["steps"][$i]["html_instructions"]



/*echo "<h1>Original</h1>";
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
echo "</pre>";*/

/*echo "<pre>";
var_dump($calc->getDirections());
echo "</pre>";*/
//echo $calc->getState(38.446655, -82.299389);


//$calc->getState($locations[0]['lat'], $locations[0]['lon']);
