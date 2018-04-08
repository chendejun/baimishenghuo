<?php
/** 
 * @Copyright: ©2014-2015 百米生活电子商务有限公司 All Rights Reserved
 * @link:  http://www.100msh.net
 * ---------------------------------------------
 * @author: sakmon/2015-02-04
 * @desc:   Redis查找类
**/
class RedisCache{
	private $host;
	private $port;
	private $prefix;
	private $redis;
	private $auth;
	private static $instance;
	private function __clone(){}
	private function __construct($host , $port = 6380, $prefix = '' , $auth = ''){
		$this->host = $host;
		$this->port = $port;
		$this->prefix = $prefix;
		$this->auth = $auth;
		$this->connect();
	}
	public static function Instance($host = '127.0.0.1' , $port = 6380, $prefix = '' , $auth = ''){
        if (!(self::$instance instanceof self)){
        	self::$instance = new self($host , $port , $prefix , $auth);
        }
        return self::$instance;
    }
	private function connect($times = 0){
		$this->redis = new Redis();
		$this->redis->pconnect($this->host , $this->port , 3);
		if(!empty($this->auth)) $this->redis->auth($this->auth);
		$this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
		if(!empty($this->prefix) ) $this->redis->setOption(Redis::OPT_PREFIX, $this->prefix );
		if(!$this->ping() && $times<3){ //避免连接池中连接因长时间没有操作被server主动断开。
			$times++;
			$this->connect($times);
		}
	}
	private function ping() {
		try {
			$pong = $this->redis->ping();
		} catch ( Exception $e ) {
			return false;
		}
		return true;
	}
    /**
     * 为 key 设置生存时间,接受的时间参数是 UNIX 时间戳(unix timestamp)
     * @param  [type] $key  [description]
     * @param  [type] $time [description]
     * @return [type]       [description]
     */
    public function expireAt($key,$time){
    	return $this->redis->expireAt($key,$time);
    }
    /**
	 * 队列左边出队
	 * @param  [type] $course [description]
	 * @return [type]         [description]
	 */
	public function lPop($course){
		return $this->redis->lPop($course);
	}
	/**
	 * 队列右边入队
	 * @param  [type] $course [description]
	 * @param  [type] $value  [description]
	 * @return [type]         [description]
	 */
	public function rPush($course,$value){
		return $this->redis->rPush($course,$value );
	}
	/**
	 * 返回队列长度
	 * @param  [type] $course [description]
	 * @return [type]         [description]
	 */
	public function lLen($course){
		return (int)$this->redis->lLen($course);
	}
	/**
	 * 获取hash表元素个数
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function hLen($key){
		return $this->redis->hLen($key);
	}
	/**
	 * 返回哈希表 key 中给定域 field 的值
	 * @param  [type] $key   [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function hGet($key,$field){
		return $this->redis->hGet($key,$field);
	}
         /**
	 * 为 key 的值加上增量 increment
	 * @param unknown $key
	 */
	public function hGetAll( $key){
		return $this->redis->hGetAll( $key);
	}
	/**
	 * 将哈希表 key 中的域 field 的值设为 value
	 * @param  [type] $hkey  [description]
	 * @param  [type] $key   [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function hSet($key,$field,$value) {
		return $this->redis->hSet($key, $field, $value );
	}
	/**
	 * 删除哈希表 key 中的一个指定域 field
	 * @param  [type] $key   [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function hDel($key,$field){
		return $this->redis->hDel($key,$field);
	}
	
	/**
	 * 判断hash里面是否存在指定域 $field
	 * @param string $key
	 * @param string $field
	 * @return boolean
	 */
	public function hExists($key , $field){
		return $this->redis->hExists($key , $field);
	}
	
	/**
	 * 一次获取多个hash域的值
	 * @param string $key
	 * @param array $fields
	 */
	public function hMget($key , $fields){
		return $this->redis->hMget($key , $fields);
	}
	/**
	 * 一次设置多个hash域
	 * @param string $key
	 * @param array $fielddata
	 */
	public function hMset($key , $fielddata){
		return $this->redis->hMset($key , $fielddata);
	}
	/**
	 * 为哈希表 key 中的域 field 的值加上增量 increment
	 * @param unknown $key
	 * @param unknown $field
	 * @param number $increment
	 */
	public function hIncrBy( $key, $field, $increment = 1 ){
		return $this->redis->hIncrBy( $key, $field, (int)$increment );
	}
	/**
	 * 为 key 的值加上增量 increment
	 * @param unknown $key
	 * @param unknown $field
	 * @param number $increment
	 */
	public function incrBy( $key, $increment = 1 ){
		return $this->redis->incrBy( $key, (int)$increment );
	}
	/**
	 * 删除单个字符串类型的 key ，时间复杂度为O(1)。
	 * 删除单个列表、集合、有序集合或哈希表类型的 key ，时间复杂度为O(M)， M 为以上数据结构内的元素数量。
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function del($key){
		return $this->redis->Del($key);
	}
	/**
	 * 返回 key 所关联的字符串值
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get($key){
		return $this->redis->get($key);
	}
	/**
	 * 将字符串值 value 关联到 key 。expire 为key设置过期时间单位s
	 * @param [type]  $key    [description]
	 * @param [type]  $value  [description]
	 * @param boolean $expire [description]
	 */
	public function set( $key, $value, $expire=FALSE ) {
        if($expire === FALSE) 	return $this->redis->set($key, $value);
        return $this->redis->setex($key, $expire, $value);
	}
	/**
	 * 检查给定 key 是否存在。
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function exists($key){
		return $this->redis->exists($key);
	}

	/**
	 * 在有序set里面添加一个元素
	 * @param unknown $key
	 * @param unknown $score
	 * @param unknown $member
	 */
	public function zAdd($key, $score, $member) {
		return $this->redis->zAdd ($key, $score, $member );
	}

	/**
	 * 返回有序结合列表
	 */
	public function zRange($key, $start, $stop, $withscore = true) {
		return $this->redis->zRange ($key, $start, $stop, $withscore );
	}
	/**
	 * 删除一个元素
	 * @param unknown $key
	 * @param unknown $member
	 */
	public function zRem($key, $member) {
		return $this->redis->zRem ($key, $member );
	}
	/**
	 * 在set里面添加一个元素
	 * @param unknown $key
	 * @param unknown $member
	 */
	public function sAdd($key, $member) {
	    return $this->redis->sAdd ($key,  $member );
	}
	/**
	 * 返回集合一个元素并删除
	 */
	public function sPop($key) {
	    return $this->redis->sPop($key);
	}
	/**
	 * 返回集合元素数量
	 */
	public function sCard($key) {
	    return $this->redis->sCard($key);
	}
	/**
	 * 返回集合元素
	 */
	public function sMembers($key) {
	    return $this->redis->sMembers($key);
	}
	/**
	 * 移除集合 key 中的一个或多个 member 元素
	 * @param  [type] $key    [description]
	 * @param  [type] $member [description]
	 * @return [type]         [description]
	 */
	public function sRem($key , $members) {
		return $this->redis->sRem($key , $members);
	}
	/**
	 * 查看KEY生存时间
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function ttl($key){
		return $this->redis->ttl($key);
	}
	/**
	 * 将信息 message 发送到指定的频道 channel
	 * @param  array $channel [description]
	 * @param  [type] $message [description]
	 * @return 接收到信息 message 的订阅者数量
	 */
	public function publish($channel,$message){
		return $this->redis->publish($channel,$message);
	}
}