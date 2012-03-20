<?php

//require_once('connector.php');

for ($x = 1; $x < 50; $x++) {
   switch ($pid = pcntl_fork()) {
      case -1:
         // @fail
         die('Fork failed');
         break;

      case 0:
         // @child: Include() misbehaving code here
         print "FORK: Child #{$x} preparing to nuke...\n";
	connector($x);
         break;

      default:
         // @parent
//         print "FORK: Parent, letting the child run amok...\n";
         break;
   }
}

         pcntl_waitpid($pid, $status);

function connector($id) {
	$r = new Redis();
	$r->connect("127.0.0.1", "6379");

	$i=1;
	$prefix = mt_rand(0,10000);
	while (true) {
		$i++;
		$value = sha1(mt_rand(0,10000)).sha1(mt_rand(0,10000)).sha1(mt_rand(0,10000));
		//$value =  sha1(mt_rand(0,10000));
		try {	
			if ( !$r->set($prefix.'_'.$i, $value) ) {
				echo "current key: _$i\n";
				break;
			}
		} catch(RedisException $e) {
			$r2 = new Redis();
			$r2->connect("127.0.0.1", "6380");
			if ( $r2->get($prefix.'_'.($i-1) ) ) {
				$status = "Ok\n";
			} else {
				$status = "Failed\n";
			}
			echo "Redis replication status on thread $id: $status";
//			echo "current key: {$prefix}_$i\n";
			exit(0);
		}
	}
}
?>
