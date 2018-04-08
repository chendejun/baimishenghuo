<?php
abstract class Model {
	protected $redis;
	protected $db;
	protected $uinfo;
	public function  __construct(){
		$this->db    = Yaf_Registry::get('db');
		$this->redis = Yaf_Registry::get('redis');
		$this->uinfo = &$_SESSION['uinfo'];
	}
}