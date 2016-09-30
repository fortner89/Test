<?php
require_once ('classes/distanceCalculator.class.php');
$calc = new distanceCalculator('AIzaSyAlzwM5vJhI8yWYibpDzib4iyE0Tmx_7ik');

/*$res = $calc->getAllPoints();*/

$res = $calc->calcStateDistance($locations, "km", 1);