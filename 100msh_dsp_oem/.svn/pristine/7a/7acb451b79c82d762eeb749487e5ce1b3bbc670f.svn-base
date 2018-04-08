<?php
class DbPdoException extends PDOException {
	public static $debug = true;
    public function __construct(PDOException $err, $config, $queryStr) {
  		// $trace = '<table border="0">';
		// foreach ($err->getTrace() as $a => $b) {
		//     foreach ($b as $c => $d) {
		//         if ($c == 'args') {
		//             foreach ($d as $e => $f) {
		//                 $trace .= '<tr><td><b>' . strval($a) . '#</b></td><td align="right"><u>args:</u></td> <td><u>' . $e . '</u>:</td><td><i>' . $f . '</i></td></tr>';
		//             }
		//         } else {
		//             $trace .= '<tr><td><b>' . strval($a) . '#</b></td><td align="right"><u>' . $c . '</u>:</td><td></td><td><i>' . $d . '</i></td>';
		//         }
		//     }
		// }
		// $trace .= '</table>';
        if(self::$debug){
        	echo '<font face="Verdana"><center><fieldset style="width: 66%;"><legend><b>[</b>PHP PDO Error ' . strval($err->getCode()) . '<b>]</b></legend> <table border="0"><tr><td align="right"><b><u>Message:</u></b></td><td><i>' . $err->getMessage() . '</i></td></tr><tr><td align="right"><b><u>Code:</u></b></td><td><i>' . strval($err->getCode()) . '</i></td></tr><tr><td align="right"><b><u>SQL:</u></b></td><td><i>' . $queryStr . '</i></td></tr><tr><td align="right"><b><u>File:</u></b></td><td><i>' . $err->getFile() . '</i></td></tr><tr><td align="right"><b><u>Line:</u></b></td><td><i>' . strval($err->getLine()) . '</i></td></tr></table></fieldset></center></font>';
    	}else{
    		header ( 'HTTP/1.1 500 Internal Server Error' );
    	}
    	exit;
    } 
    static public function setDebug($v){
        self::$debug=$v;
	}
}