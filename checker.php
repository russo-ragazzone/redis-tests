<?php
require_once('connector.php');

// 8705 - 20381
// 5090 - 20381
// 9473 - 20384
// 7275 - 20390
// 4448 - 20386
$broken_keys;

for($i=1;$i>0; $i++ ){
	$key = '8705'.'_'.$i;
	if ( !$r->get($key) ) {
		if (!$broken_keys) echo "Key $key not found\n";
		$broken_keys++;
	} else {
		$broken_keys = 0;
	}
	if ( $broken_keys > 50 ) {
		break;
	}
}



?>
