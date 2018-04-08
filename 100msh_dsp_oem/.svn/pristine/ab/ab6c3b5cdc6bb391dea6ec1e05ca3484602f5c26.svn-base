<?php
class Helper
{
	private static $models = array();
	
	static function M( $name ){
		$moduleName = Yaf_Registry::get('moduleName');
		if(isset(self::$models[$moduleName][$name]) ){
			return self::$models[$moduleName][$name];
		}
		$path = APP_PATH."/application/modules/$moduleName/models/{$name}.php";
		if(!file_exists($path)){
			$path =APP_PATH.'/application/models/'.$name.'.php';
		}
		Yaf_Loader::import($path);
		self::$models[$moduleName][$name] = new $name;
		return self::$models[$moduleName][$name];
	}
	/**
	 * [logger description]
	 * @param  [type] $msg  [description]
	 * @param  string $type [description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	static function logger($msg , $type = '' , $path = 'Analy'){
	    if (empty($msg))   return false;
	    if (empty($type))  $type = 'debug';

	    if (!is_string($msg))     $msg = var_export($msg, true);
	    
	    $msg = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;

	    $maxSize = 2097152;//2M
	    list($y, $m, $d) = explode('-', date('Y-m-d'));
	    $dir = LOG_PATH . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $y . $m;
	    $file = "{$dir}/{$type}-{$d}.log";
	    if (!file_exists($dir)) {
	        mkdir($dir, 0777 , true);
	    }
	    if (file_exists($file) && filesize($file) >= $maxSize) {
	        $a = pathinfo($file);
	        $bak = $a['dirname'] . DIRECTORY_SEPARATOR . $a['filename'] . '-'. date('His') .'.' . $a['extension'];
	        if (!rename($file, $bak)) {
	            echo "rename file:{$file} to {$bak} failed";
	        }
	    }
	    error_log($msg, 3, $file);
	}
	/**
	 * 获取客户端IP
	 * @return [type] [description]
	 */
	static function get_client_ip( ){
	    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	    	$ip = getenv("HTTP_CLIENT_IP");
	    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	    	$ip = getenv("HTTP_X_FORWARDED_FOR");
	    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	    	$ip = getenv("REMOTE_ADDR");
	    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	    	$ip = $_SERVER['REMOTE_ADDR'];
	    else
	    	$ip = "unknown";

	    if(!preg_match("/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/", $ip) ){ //IP格式错误
	        $ip = '';
	    }
	    return $ip;
	}
	static function export_csv($filename,$data)   {   
	    header("Content-type:text/csv");   
	    header("Content-Disposition:attachment;filename=".$filename);   
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
	    header('Expires:0');   
	    header('Pragma:public');   
	    echo $data;   
	}
	static function clean($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);
				$data[clean($key)] = clean($value);
			}
		} else { 
			$data = htmlspecialchars(trim($data), ENT_COMPAT, 'UTF-8'); //仅编码双引号
			if(!get_magic_quotes_gpc()) {
				$data = addslashes(urldecode($data));//转义单双引号
			}
		}

		return $data;
	}
	static function outJson($arr){
		echo json_encode($arr);
		exit;
	}
	/**
	 * 金额转换
	 * @param  [type]  $num  [description]
	 * @param  integer $type 0元转分，1分转元
	 * @return [type]        [description]
	 */
	static function transAmount($num , $type = 0){
		if($type == 0){
			$num = $num * 100;
		}else{
			$num = $num/100;
		}
		return substr(sprintf("%.3f", $num), 0, -1);
	}
	/**
	 * 下载资源文件保存到指定文件
	 * @param  [type] $file_source [description]
	 * @param  [type] $file_target [description]
	 * @return [type]              [description]
	 */
	static function download($file_source, $file_target) {
		ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36');
		$dir = dirname($file_target);
		if (!file_exists($dir)) {
	        mkdir($dir, 0777 , true);
	    }
	    $rh = fopen($file_source, 'rb');
        if ($rh===false) {
           return true;
        }
        $wh = fopen($file_target, 'w+b');
        while (!feof($rh)) {
            if (fwrite($wh, fread($rh, 1024)) === FALSE) {
                return true;
            }
        }
        fclose($rh);
        fclose($wh);
        return false;
    }
    /**
     * 下载远程大文件保存到本地
     * 
     */
    static function download_file($url)
    {
        $file = basename($url);
        $header = get_headers($url, 1);
        $size = $header['Content-Length'];
        $fp = @fopen($url, 'rb');
        if ($fp === false) return false;
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $size);
        ob_clean();
        ob_end_flush();
        set_time_limit(0);
        $chunkSize = 1024 * 1024;
        while (!feof($fp)) {
            $buffer = fread($fp, $chunkSize);
            echo $buffer;
            ob_flush();
            flush();
        }
        fclose($fp);
        return true;
    }
    static function getHost($is_http_type = false){
    	$http_type = '';
    	if($is_http_type){
    		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; 
    	}
    	return $http_type . $_SERVER['HTTP_HOST'];
    }
    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return number
     */
	static function diffDays ($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 > $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second2 - $second1) / 86400;
    }
}