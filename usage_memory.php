<?php
ini_set('error_reporting', 'E_ALL');
ini_set('display_errors', 'On');

$r = new Redis();
$r->connect("127.0.0.1", "6379");


// Calculates the hash name...
function hkey($key){
	$maxlen = 10;
	$prefix = "_";

	return $prefix . substr(md5($key.time()), 0, $maxlen);
}

// set key...
function hset($r, $key, $val){
	$key1 = hkey($key);

	return $r->hset($key1, $key, $val.time());
}

// get key...
function hget($r, $key, $val){
	$key1 = hkey($key);

	return $r->hget($key1, $key);
}
// ======================================

// Non optimized version
function hset1($r, $key, $val){
	return $r->set($key, $val.time());
}

for($i=0; $i < 2000000; $i++) {
	if ( ! ($i % 100000) ) {
		echo "Complited $i keys\n";
	}
	hset1($r, "key:" . $i, $i);
}

?>
