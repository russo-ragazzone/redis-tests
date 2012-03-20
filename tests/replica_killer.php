<?php

//require_once('connector.php');


for ($x = 1; $x <= 5; $x++) {
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

$killed = 50 ;

$servers = array( '6379', '6380' );
$server_id = 0; 

$r0 = new Redis();
$r1 = new Redis();
sleep(1);
while($killed ) {
	$sleep = 2 + mt_rand (0, 15);
	echo "Sleeping for $sleep seconds\n";
	sleep($sleep);
	unset($str,$pid, $d, $output);
	exec("netstat --tcp -nlp | grep $servers[$server_id]", $output, $code);
	$str=join($output);
	preg_match('%(\d+)\/redis-server%si', $str, $d);
	if ( empty($d[1]) ) {
		echo "Empty pid!\n";
		exit(0);
	}
	$pid = $d[1];
	echo "Server 127.0.0.1:$servers[$server_id] with PID: $pid will be killed: ";
	exec("kill -KILL $pid"); // server killed!
	$instance_slave = "r$server_id";
	// change master server ID	
	$server_id = abs($server_id - 1 );
	$instance_master = "r$server_id";
	// Reset slave on a new master:
	$$instance_master->connect('127.0.0.1',  $servers[$server_id]);
	$$instance_master->slaveof();
	// Run again old master:
	sleep(1);
	exec("redis-server /etc/redis/".$servers[abs($server_id-1)].".conf", $output);
	//echo join($output,"\n");;
	sleep(2);
	$$instance_slave->connect('127.0.0.1',$servers[abs($server_id-1)]);
	// Set as slave to new master:
	$$instance_slave->slaveof('127.0.0.1', $servers[$server_id]);
	echo "Swapping complete!\n";
	
	
	$killed --;
}

pcntl_waitpid($pid, $status);

function connector($id) {
	$servers = array( '6379', '6380' );
	$prefix = mt_rand(0,10000);
	$port = 0;

	$r = new Redis();
	$r->connect("127.0.0.1", $servers[$port]);		
	$i=0;
	while (true) {
		usleep(10000);
		if ( $i > 100000 ) {
			break;
		}
		$i++;
		$value = sha1(mt_rand(0,10000)).sha1(mt_rand(0,10000)).sha1(mt_rand(0,10000));
		//$value =  sha1(mt_rand(0,10000));
		try {	
			if ( !$r->set($prefix.'_'.$i, $value) ) {
				echo "Can't set key: {$prefix}_$i\n";
				exit(0);
			}
		} catch(RedisException $e) {
			echo "Reconnect to new slave\n";
			$r = new Redis();
			$port = abs($port - 1); // 0 or -1
			$r->connect("127.0.0.1", $servers[$port]);
			if ( $r->ping() ) {
				echo "Redis thread $id switched to 127.0.0.1:$servers[$port]\n";
			} else {
				print("Switching to new instance failed!\n");
				exit(1);
			}
			if ( !$r->set($prefix.'_'.$i, $value) ) {
				echo "Can't set key: {$prefix}_$i\n";
				exit(0);
			}
		}
	}
}
?>
