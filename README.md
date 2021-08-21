# phpserial
serial port access class

```bash
$ php composer.phar require "crucifyer/phpserial" "dev-main"
```

```php
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
```