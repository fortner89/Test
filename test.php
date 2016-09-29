<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$locations = array(
	'"38.446655, -82.299389"',
	'"38.136234, -92.912181"',
	'"38.830008, -94.518546"'
	);
$locations = implode(",", $locations);
var_dump($locations);
$curl = curl_init();
$url = 'http://www.mapquestapi.com/directions/v2/route?key=GYpdBizrOkgQzgNRcQSZbRMZ3TAIYEGA&callback=renderAdvancedNarrative&ambiguities=ignore&outFormat=json&inFormat=json&json={locations:['.$locations.'],options:{avoids:[],avoidTimedConditions:false,doReverseGeocode: false,routeType:fastest,timeType:1,locale:en_US,unit:m,enhancedNarrative:false,drivingStyle: 2,highwayEfficiency: 21.0}}';

$url = str_replace(array('"', " "), array("%22", "%20"), $url);

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);
$result = str_replace('renderAdvancedNarrative(', '{"renderAdvancedNarrative":', $result);
$result = substr_replace($result, "}", -2);
echo $result;
$result = json_decode($result, true);
echo $result;
echo "<pre>";
var_dump($result);
echo "</pre>";