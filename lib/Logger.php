<?php
/**
* Simple Logger Class
*
* @author Josh Nesbitt <josh@josh-nesbitt.net>
*
* By default will write to path/to/logger/ + log/filename.log
*
**/
class Logger {
  var $file, $path, $level, $stream;
  const INFO  = 4;
  const DEBUG = 3;
  const WARN  = 2;
  const ERROR = 1;
  const FATAL = 0;
  
	function __construct($file, $level,$path = null)
	{
		$this->file = $file;
		$this->level = $level;
		$this->path = ( is_null( $path )? $_SERVER["DOCUMENT_ROOT"]. "/log" : $path)."/".$this->file;
		$this->start();
	}
	
	function info($string)
	{
	  return $this->check_level(self::INFO) ? true : $this->log("INFO: ".$string);
	}
	
	function warn($string)
	{
	  return $this->check_level(self::WARN) ? true : $this->log("WARN: ".$string);
	}
	
	function debug($string)
	{
	  return $this->check_level(self::DEBUG) ? true : $this->log("DEBUG: ". $string);
	}
	
	function error($string)
	{
	  return $this->check_level(self::ERROR) ? true : $this->log("ERROR: ".$string);
	}
	
	function fatal($string)
	{
	  return $this->check_level(self::FATAL) ? true : $this->log("FATAL: ".$string);
	}
	
	function clear()
	{
	  $this->close();
	  $this->open("w");
	  $this->close();
	  $this->open();
	}
	
	private function check_level($level)
	{
	  return $this->level < $level;
	}
	
	private function log($string)
	{
	  $this->write("[". date('l jS F Y : h:i:sa') . "] ". $string . "\r\n");
	}
  
	private function write($string)
	{
	  return fwrite($this->stream, $string);
	}
  
	private function start()
	{
	  return $this->open();
	}
	
	private function open($mode="a")
	{
	  return $this->stream = fopen($this->path, $mode) or die("Cannot write to file '$this->path', please ensure '$this->path' is writable.");
	}
	
	private function close()
	{
	  return fclose($this->stream);
	}
	
}

?>