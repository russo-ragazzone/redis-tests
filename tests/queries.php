<?php

$sizes = array (1, 512, 1024, 1024*50, 1024*100, 1024*500, 1024*1024  );


foreach($sizes as $size ) {
	# Force reset:
	echo "====== Size $size bytes ======\n";
	system( "/etc/init.d/redis_6379 stop > /dev/null");		
	system( "rm -rf /var/lib/redis/6379/dump.rdb");
	system( "/etc/init.d/redis_6379 start > /dev/null");
	system( "redis-benchmark -q -r 10 -d $size");
	echo " -------------- \n\n\n\n";
}


?>
