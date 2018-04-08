<?php
include("DbPdoException.php");
class Db{
	public $config = array();
	private $links  = array();
	public function __construct($config){
		$this->config = $config;
	}
	public function __get($name){
		if(isset($this->links[$name])){
			return $this->links[$name];
		}elseif(isset($this->config[$name]) ){
			$this->links[$name] = new Core_MysqlPdo($this->config[$name]);
			return $this->links[$name];
		}
		BmException::errorLog("Can't find {$name} db config !");
	}
	/**
	 * 添加数据库
	 * @param [type] $ln  [description]
	 * @param [type] $cfg [description]
	 */
	public function add( $ln , $cfg){
		if(!isset($this->links[$ln])){
			$this->links[$ln] = new Core_MysqlPdo($cfg);
		}
	}
}