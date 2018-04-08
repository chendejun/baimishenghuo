<?php
class Account extends Model{
    public function getAccountInfoByCompId($comp_id,$type){
        $account_info = $this->db->account->query("SELECT * FROM `dsp_account` WHERE `comp_id`={$comp_id} AND `account_type`={$type} LIMIT 1");
        if(!empty($account_info)) return $account_info[0];
        return array();
    }
    /**
     * 廣告主列表
     * @param  [type]  $where [description]
     * @param  integer $start [description]
     * @param  integer $limit [description]
     * @return [type]         [description]
     */
    public function getAdverList($where , $start = 0 , $limit=15){
        $result = $this->db->dsp->query("SELECT comp_id,comp_name,contacts,mobile_phone,gdt_apply_status FROM `dsp_company` WHERE $where ORDER BY `comp_id` DESC LIMIT {$start},{$limit}");
        if(!empty($result)){
            $comp_ids = array();
            foreach ($result as $k => $v) {
                $comp_ids[] = $v['comp_id'];
            }
            $acc_list_0 = $this->db->account->query("SELECT comp_id,usable_amount,freeze_amount,total_disburse FROM `dsp_account` WHERE account_type=0 AND comp_id IN(" . implode(',', $comp_ids) . ")");
            $acc_amounts_0 = array();
            foreach ($acc_list_0 as $k0 => $v0) {
                $acc_amounts_0[$v0['comp_id']] = $v0;
            }
            $acc_list_1 = $this->db->account->query("SELECT comp_id,usable_amount,freeze_amount,total_disburse FROM `dsp_account` WHERE account_type=1 AND comp_id IN(" . implode(',', $comp_ids) . ")");
            $acc_amounts_1 = array();
            foreach ($acc_list_1 as $k1 => $v1) {
                $acc_amounts_1[$v1['comp_id']] = $v1;
            }
            $acc_list_2 = $this->db->account->query("SELECT comp_id,usable_amount,freeze_amount,total_disburse FROM `dsp_account` WHERE account_type=2 AND comp_id IN(" . implode(',', $comp_ids) . ")");
            $acc_amounts_2 = array();
            foreach ($acc_list_2 as $k2 => $v2) {
                $acc_amounts_2[$v2['comp_id']] = $v2;
            }
            foreach ($result as $k => $v) {
                $result[$k]['cash_amount'] = $acc_amounts_1[$v['comp_id']]['usable_amount'];
                $result[$k]['virtual_amount'] = $acc_amounts_0[$v['comp_id']]['usable_amount'];
                $result[$k]['give_amount'] = $acc_amounts_2[$v['comp_id']]['usable_amount'];
                $result[$k]['total_disburse'] = $acc_amounts_0[$v['comp_id']]['total_disburse']+$acc_amounts_1[$v['comp_id']]['total_disburse']+$acc_amounts_2[$v['comp_id']]['total_disburse'];
            }
        }
        return $result;
    }
    public function getAdverCount($where){
        $result = $this->db->dsp->query("SELECT count(1) as num FROM `dsp_company` WHERE {$where}");
        return empty($result)?0:$result[0]['num'];
    }
    /**
     * 通过千里眼代理商ID查找DSP系统服务商ID
     * @param  [type] $pf_id [description]
     * @return [type]        [description]
     */
    public function getCompIdByAgId($pf_id){
        $rows = $this->db->dsp->query("SELECT comp_id FROM dsp_company WHERE pf_id='{$pf_id}' AND comp_type=2 LIMIT 1");
        if(empty($rows)) return 0;
        return $rows[0]['comp_id'];
    }
    /**
     * 通过接口创建商户
     * @return [type] [description]
     */
    public function createComp($data,$img_data){
        $agency_id = $data['agency_id'];
        unset($data['agency_id']);
        //先判断该商户有没有被创建
        $rows = $this->db->dsp->query("SELECT comp_pid,comp_id FROM dsp_company WHERE pf_id='{$data['pf_id']}' AND comp_type=1 LIMIT 1");
        if(!empty($rows)){ //已经创建的更新信息
            if($rows[0]['comp_pid'] != $agency_id){ //代理商ID发生了变化
                $comp_pid = $this->getCompIdByAgId($agency_id);
                if($comp_pid == 0){ //新的代理商没有进来
                    $data['flag'] = 0; //标识商户是否有关联服务商，0没有，1有
                    $data['comp_pid'] = -$agency_id; //没关联服务商时候，服务商ID为负原代理商ID
                }else{
                    $data['flag'] = 1;
                    $data['comp_pid'] = $comp_pid;
                }
            }
            $this->db->dsp->table("dsp_company")->data($data)->where("pf_id='{$data['pf_id']}' AND comp_type=1")->update();
            /**2017-11-15 V1.2**/
            $img_rows=$this->db->dsp->query("SELECT comp_id FROM dsp_comp_img WHERE comp_id='{$rows[0]['comp_id']}' LIMIT 1");
            if(!empty($img_rows)){
                $this->db->dsp->table("dsp_comp_img")->data($img_data)->where("comp_id='{$rows[0]['comp_id']}'")->update();
            }else{
                $img_data['comp_id']=$rows[0]['comp_id'];
                $this->db->dsp->table("dsp_comp_img")->data($img_data)->insert();
            }
            /**2017-11-15 V1.2**/
            return 1;
        }else{ //没有创建，新建广告主
            $data['comp_type'] = 1;
            $comp_pid = $this->getCompIdByAgId($agency_id); //通过商户所属代理上ID查找对应DSP系统中的服务商ID
            if($comp_pid == 0){
                $data['flag'] = 0; //标识商户是否有关联服务商，0没有，1有
                $data['comp_pid'] = -$agency_id; //没关联服务商时候，服务商ID为负原代理商ID
            }else{
                $data['flag'] = 1;
                $data['comp_pid'] = $comp_pid;
            }
            $comp_id = $this->db->dsp->table("dsp_company")->data($data)->insert();
            if(!$comp_id){
                return false;
            }
            /**2017-11-15 V1.2**/
            $img_data['comp_id']=$comp_id;
            $this->db->dsp->table("dsp_comp_img")->data($img_data)->insert();
            /**2017-11-15 V1.2**/
            $account_id = $this->db->account->execute("INSERT INTO `dsp_account`(`comp_id`,`account_type`) VALUES({$comp_id},0),({$comp_id},1),({$comp_id},2)");
            return true;
        }
        return false;
    }
    public function setMobile($comp_id , $mobile_phone){
        return $this->db->dsp->execute("UPDATE dsp_company SET `mobile_phone`='{$mobile_phone}' WHERE comp_id={$comp_id}");
    }

	public function getComp_ids(){
		// echo $this->uinfo['user_group']."====";
		if($this->uinfo['user_group'] == ROLE_OPERATED){
			$result = $this->db->dsp->query("SELECT comp_id FROM `dsp_company` WHERE comp_type=1 and ascription_staff_id={$this->uinfo['staff_id']}");
	        if(!empty($result)){
	            $comp_ids = array();
	            foreach ($result as $k => $v) {
	                $comp_ids[] = $v['comp_id'];
	            }
	            return implode(',', $comp_ids);
	        }else{
	        	return "-100";
	        }
		}else{
			return "";
		}
    }
}
