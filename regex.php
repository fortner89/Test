<?php
$url = "www.exampe.com/id/1234";
preg_match('(id/(\d+))', $url, $matches);
echo "<pre>";
var_dump($matches);
echo "</pre>";

$url = "Keep left to take I-64 W via EXIT 118 toward Frankfort/Louisville (Passing through Indiana, then crossing into Illinois Indiana).";
preg_match('(\(Passing through (.+)(,))', $url, $matches);
echo "<pre>";
var_dump($matches);
echo "</pre>";
?>