<?php
$locations = array(
	array('lat' => 38.446655, 'lon' => -82.299389),
	array('lat' => 38.136234, 'lon' => -92.912181),
	array('lat' => 38.830008, 'lon' => -94.518546),
	array('lat' => 38.830008, 'lon' => -94.518546),
	array('lat' => 38.446655, 'lon' => -82.299389),
	array('lat' => 38.136234, 'lon' => -92.912181),
	array('lat' => 38.830008, 'lon' => -94.518546),
	array('lat' => 38.830008, 'lon' => -94.518546),
	array('lat' => 38.446655, 'lon' => -82.299389),
	array('lat' => 38.136234, 'lon' => -92.912181),
	array('lat' => 38.830008, 'lon' => -94.518546),
	array('lat' => 38.830008, 'lon' => -94.518546),
	array('lat' => 38.446655, 'lon' => -82.299389),
	array('lat' => 38.136234, 'lon' => -92.912181),
	array('lat' => 38.830008, 'lon' => -94.518546),
	array('lat' => 38.830008, 'lon' => -94.518546)
	);
$max = count($locations);
//echo $max%5;
for ($i=0; $i < count($locations); $i++) { 
	echo $i;
	if(($i+1)%5 == 0){
		echo " here<br/>";
	}
}

/*if(($i+1)%25 == 0){
	echo " here<br/>";
}*/