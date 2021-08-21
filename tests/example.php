<?php

chdir(__DIR__);
include_once '../vendor/autoload.php';

$xserial = new \Xeno\Net\Serial();
$xserial->setLog('serial.log', \Xeno\Net\Serial::LOG_ALL);
$xserial->setEcho(true);
$xserial->writeLn('AT');
$xserial->readLn();
$xserial->readLn();

/*
W: AT
R: AT
R: OK
 */

// listen
while(true) {
	$line = $xserial->readLn(false, 10);
	if($line == '') {
		sleep(5);
		continue;
	}
	echo $line;
}

exit;