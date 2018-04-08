<?php
class Router  extends Yaf_Request_Abstract implements Yaf_Route_Interface{
	/**
	 * [route description]
	 * @param  [type] $req [description]
	 * @return [type]      [description]
	 */
    public function route ($req){
    	$s = strpos($_SERVER['REQUEST_URI'] , '?');
    	if($s){
    		$str = substr($_SERVER['REQUEST_URI'] , 0 ,$s);
    	}else{
    		$str = $_SERVER['REQUEST_URI'];
    	}
        $uri = explode('/', trim($str , '/') );
        $x = 0;
        if(in_array(strtolower($uri[0]) , array('shopapiv2','sms','admin') ) ) {
            $req->module = ucwords(strtolower($uri[0]));
            $req->controller = !empty($uri[1])?$uri[1]:'';
            $req->action = !empty($uri[2])?$uri[2]:'';
            $x = 3;
        }else{
        	$req->module = 'Index';
        	$req->controller = !empty($uri[0])?$uri[0]:'';
        	$req->action = !empty($uri[1])?$uri[1]:'';
        	$x = 2;
        }
        if(count($uri) > 3){
            $param = array();
            $params = array_slice($uri, $x);
            foreach ( $params as $key => $value) {
                if( $key %2 == 0){
                    $param[$params[$key]] = $params[$key+1];
                }
            }
            $req->params = $param;
        }
        return true;
    }

    public function assemble (array $mvc, array $query = NULL){
        return true;
    }
}