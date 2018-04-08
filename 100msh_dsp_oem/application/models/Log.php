<?php
/**
 * @desc: 日志管理
**/

class Log extends Model{

	private $log_cate_arr=array ('LC001' => 1,'LC002' => 2,'LC003' => 3 ,'LC004' => 4 ,'LC005' =>5 ,'LC006'=>6,'LC007'=>7,'LC008');
	private $oper_type_arr=array('LOT001' => 1, 'LOT002' => 2 ,'LOT003' => 3 ,'LOT004' => 4 ,'LOT005' => 5);


	/**
	* @desc 创建日志
	* @param $log_title   日志标题
	* @param $log_content 日志内容
	* @param $log_cate    日志分类(LC001登录日志,LC002我的账号,LC003广告管理,LC004效果报告,LC005人群包管理,LC006我的广告主,LC007设备管理,LC008代理商管理)
	* @param $log_oper_type   日志操作类型(LOT001添加，LOT002修改，LOT003删除，LOT004查询，LOT005其他)
	* @return array 成功或失败信息
	**/
	public function create_log($log_title,$log_content,$log_cate,$log_oper_type='LOT005',$userid=""){
		global $user_type;
		$user_type=1;
			$user_id   = $this->uinfo['staff_id'];
            $user_name = $this->uinfo['user_name'];
            $real_name = $this->uinfo['staff_name'];
			if(empty($user_id)){
				return array(
				    "is_success" => false,
				    "info" => "用户未登录!",
				);
			}//用户未登录

		if(!array_key_exists($log_cate,$this->log_cate_arr)){
			$log_cate_arr_A=$this->get_log_cate_arr();
			if(!array_key_exists($log_cate,$log_cate_arr_A)){
				return array(
				"is_success" => false,
				"info" => "日志分类不正确!",
				);
			}
			$this->log_cate_arr = $log_cate_arr_A;
		}//日志分类不存在
		if(!array_key_exists($log_oper_type,$this->oper_type_arr)){
			$oper_type_arr_A=$this->get_log_oper_arr();
			if(!array_key_exists($log_oper_type,$oper_type_arr_A)){
				return array(
				"is_success" => false,
				"info" => "日志操作类型不正确!",
				);
			}
			$this->oper_type_arr = $oper_type_arr_A;
		}//日志操作类型不存在
		if(empty($log_title) || empty($log_cate) || empty($log_oper_type) || empty($user_type)|| empty($log_content)){
			return array(
			"is_success" => false,
			"info" => "日志写入失败，参数为空!",
			);
		}
		$log_data= array(
			"oper_user_id"          => $user_id,
			"oper_user_name"        => $user_name,
			"oper_user_realname"    => $real_name,
			"oper_user_type"        => $user_type,
			"log_cate_id"           => $this->log_cate_arr[$log_cate],
			"log_oper_type_id"      => $this->oper_type_arr[$log_oper_type],
			"log_title"             => $log_title,
			"log_content"           => $log_content,
			"log_ip"                => Helper::get_client_ip(),
			"log_time"              => time(),
		);
		//日志表以月份进行分表插入
		$nowm=date("Ym",time());
		$lastday=Date('t',time());
		//查找库里是否存在当前月的日志表，如：anl_log_201302
		$nowm_tab="dsp_log_".$nowm;
		$findresult='';
		$results=$this->db->dsp->query("SHOW TABLES LIKE '$nowm_tab'");
		if(!empty($results)) {
			$this->db->dsp->table($nowm_tab)->data($log_data)->insert();
			$this->db->dsp->execute("UPDATE `dsp_log_cate` SET `used`='1' WHERE used=0 AND cate_no='$log_cate'");
			return  array(
				"is_success" => true,
				"info" => "操作成功!",
			);
		} else {
			//创建该表
			$carete_tal="CREATE TABLE IF NOT EXISTS `$nowm_tab` (
                     `log_id` int(11) NOT NULL AUTO_INCREMENT,
                     `oper_user_id` int(11) NOT NULL,
                     `oper_user_name` varchar(20) NOT NULL,
                     `oper_user_realname` varchar(20) NOT NULL,
                     `oper_user_type` tinyint(1) NOT NULL default '1',
                     `log_cate_id` int(11) NOT NULL,
                     `log_oper_type_id` int(11) NOT NULL,
                     `log_title` varchar(100) NOT NULL,
                     `log_content` text,
                     `log_time` int(11) NOT NULL,
                     `log_ip` varchar(30) NOT NULL,
                      PRIMARY KEY (`log_id`)
                   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
			$res_c = $this->db->dsp->execute($carete_tal);
			if(empty($res_c)){
				//创建一张年月表
				$ym_date=array(
				"partition_title"=>date("Y-m",time()),
				"partition_start"=>strtotime($nowm.'01 00:00:00'),
				"partition_end"=>strtotime($nowm.$lastday.' 23:59:59'),
				"partition_table"=>'dsp_log_'.$nowm,
				"partition_desc"=>'按月分表',
				);
				$res_ym=$this->db->dsp->table('dsp_log_time_partition')->data($ym_date)->insert();
				if($res_ym){
					$this->db->dsp->table($nowm_tab)->data($log_data)->insert();
					$this->db->dsp->execute("UPDATE `dsp_log_cate` SET `used`='1' WHERE used=0 AND cate_no='$log_cate'");
					return  array(
						"is_success" => true,
						"info" => "操作成功!",
					);
				}else{
					return  array(
					"is_success" => false,
					"info" => "日志分表年月表信息插入不成功!",
					);
				}

			}else{
				return  array(
				"is_success" => false,
				"info" => "日志表创建不成功!",
				);
			}
		}
	}
	/**
     * @desc 获得日志分类表的cate_name与log_cate_id的健值对
     * @return array
     */
	function get_log_cate_arr(){
		$result=$this->db->dsp->query("SELECT log_cate_id,cate_no FROM dsp_log_cate WHERE 1 ORDER BY log_cate_id ASC");
		$list = array();
		if(!empty($result)) {
			foreach($result as $row){
				$list[$row['cate_no']] = $row['log_cate_id'];
			}
		}
		
		return $list;
	}

	/**
     * @desc 获得日志操作类型表type_name与log_oper_type_id的健值对
     * @return array
     */
	function get_log_oper_arr(){
		$result=$this->db->dsp->query("log_oper_type_id,type_no FROM dsp_log_oper_type ORDER BY log_oper_type_id ASC");
		$list = array();
		if(!empty($result)) {
			foreach($result as $row){
				$list[$row['type_no']] = $row['log_oper_type_id'];
			}
		}
		return $list;
	}
}
?>