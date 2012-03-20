<?php

ini_set('error_reporting', 'E_ALL');
ini_set('display_errors', 'On');

$r = new Redis();
$r->connect("127.0.0.1", "6379");



?>
