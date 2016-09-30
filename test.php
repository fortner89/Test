<?php
require_once ('classes/distanceCalculator.class.php');
$calc = new distanceCalculator('GOOGLE_MAPS_API_KEY');

//Retrieve all datapoints from the database
$res = $calc->getAllPoints();

//Calculate miles traveled in state and produce a table with an output in miles
$calc->calcStateDistance($res, "mi", 1);