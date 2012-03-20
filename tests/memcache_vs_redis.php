<?php



define('REDIS_TEST', true);


//if ( !defined('REDIS_TEST')) {
//	define('MEMCACHED_TEST', true);
//}


if ( defined('REDIS_TEST') ) {
	echo "Redis test ".date('H:i:s'). " \n";
	$db = new Redis;
	$db->connect('127.0.0.1', 6379 );
} else { // MEMCACHED_TEST
	echo "Memacache test ".date('H:i:s'). " \n";
	$db = new Memcache;
	$db-> connect('127.0.0.1', 11211);
	$version = $db->getVersion();
	echo "Версия сервера: ".$version."\n";	
}


$i = 1;
$g_err = 0;
$stime = microtime(true);
while ( $i <= 1000000 ) {
	$value = sha1( $i ) ;
	$key = $value;
	if ( 0 &&  defined('REDIS_TEST') ) {
		$key_name = substr($key,0,3);
		$db->hset($key_name, $key, $value) ;
	} else {
		if ( !$db->set($key, $value) ) {
//		if ( !$db->get($key) ) {
			$g_err++;
//			echo "\nGet error!\n";
		}
	}
	if ( !($i%10000 ) ){
		$p = $i / 10000;
		echo "Complited $p% percents.\r";
	}
	$i++;
}
$ttime = microtime(true) - $stime;
$reqs = round(1000000/$ttime,4);
echo "\nErrors: $g_err\n";
echo "Average: $reqs req/s\n";
echo "Total time: $ttime\n";


?>
