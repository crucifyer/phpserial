<?php

namespace Xeno\Net;

class Serial
{
	const LOG_NONE = 0, LOG_WRITE = 1, LOG_READ = 2, LOG_ALL = 3;
	private $fp, $echo = false, $ln = "\r\n", $usleep = 500, $loglevel = 2, $logfile = null;

	public function __construct($port = '/dev/serial0', $baudrate = 115200, $mode = 'r+b') {
		if(!file_exists($port)) throw new \ErrorException("$port not found");
		shell_exec(sprintf('stty -F '.$port.' %s cs8 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts', $baudrate));
		$this->fp = fopen($port, $mode);
		if(!$this->fp) throw new \ErrorException("$port open failed");
		stream_set_blocking($this->fp, false);
	}

	public function setEcho($flag) {
		$this->echo = $flag;
	}

	public function setLog($file, $level) {
		$this->logfile = $file;
		$this->loglevel = $level;
	}

	public function setUsleep($usleep = 500) {
		$this->usleep = $usleep;
	}

	public function setLineBreak($ln) {
		$this->ln = $ln;
	}

	private function logSave($text) {
		if(!$this->logfile || !$this->loglevel) return;
		file_put_contents($this->logfile, $text, FILE_APPEND);
	}

	private function log($type, $text) {
		if($text == '') return;
		$text = "$type: $text";
		if($this->echo) echo $text;
		if($this->loglevel & self::LOG_WRITE && $type == 'W') $this->logSave($text);
		else if($this->loglevel & self::LOG_READ && $type == 'R') $this->logSave($text);
	}

	public function write($text) {
		$len = strlen($text);
		while($len > 0) {
			$len -= fwrite($this->fp, substr($text, -$len));
			fflush($this->fp);
		}
		$this->log('W', $text);
		if($this->usleep) usleep($this->usleep);
	}

	public function writeLn($text) {
		$this->write($text.$this->ln);
	}

	public function readLn($block = true, $wait = 10) {
		$ln = substr($this->ln, -1);
		$res = '';
		$l = $wait;
		while ($block || $l > 0) {
			$c = fgets($this->fp, 512);
			if($c == '') {
				$l--;
				if($this->usleep) usleep($this->usleep);
				continue;
			}
			$l = $wait;
			$res .= $c;
			if(substr($c, -1) == $ln) break;
		}
		$this->log('R', $res);
		if($this->usleep) usleep($this->usleep);
		return $res;
	}

	public function flush() {
		fflush($this->fp);
	}
}