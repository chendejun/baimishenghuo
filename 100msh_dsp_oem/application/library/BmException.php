<?php
class BmException{
	const ENV = 'dev';
	public static function errorHandler($errno, $errstr, $errfile, $errline){
		switch ($errno) {
		     case YAF_ERR_NOTFOUND_CONTROLLER:
		     case YAF_ERR_NOTFOUND_MODULE:
		     case YAF_ERR_NOTFOUND_ACTION:
		         header("Not Found");
		     break;

		     default:
		        	$msg = "Unknown error type: [$errno] $errstr thrown in $errfile on line $errline";
		        	self::errorLog($msg);
		       	 break;  
		}
		return true;
	}
	public static function errorLog($msg){
		if(self::ENV == 'dev'){
			echo $msg;
		}else{
			Helper::logger($msg , 'sys' , 'sys');
		}
		return true;
	}
}