<?php

require_once('connector.php');

$i=1;
while (true) {
	$i++;
	$value = sha1(mt_rand(0,10000)).sha1(mt_rand(0,10000)).sha1(mt_rand(0,10000));
	//$value =  sha1(mt_rand(0,10000));
	$r->hset('superkey', '_'.$i, $value);
}

?>
