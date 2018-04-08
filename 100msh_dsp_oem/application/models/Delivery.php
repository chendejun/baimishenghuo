<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/27
 * Time: 18:46
 */
Class Delivery extends  Model{
    /*
     * 添加投放
     */
    public function add($data){
       $deliveryId  = $this->db->dsp->table("dsp_delivery")->data($data)->insert();
       return $deliveryId;
    }
    /**
     * 投放列表
     */
    public function getDeliveryCount($where){
        $result = $this->db->dsp->query("SELECT count(1) as num FROM `dsp_delivery` AS deli JOIN dsp_company AS comp ON comp.comp_id = deli.comp_id  WHERE {$where}");
        return empty($result)?0:$result[0]['num'];
    }
    public  function getDeliveryList($where,$start,$limit){
         $result = $this->db->dsp->query("SELECT delivery_id,delivery_name,deli.comp_id,comp.comp_name as comp_name,compy.staff_name as add_staff_name,delivery_start_date,delivery_status,day_money,bid_type,bid_money,deli.add_time,ad_type FROM `dsp_delivery` AS deli
                   JOIN dsp_company AS comp ON comp.comp_id = deli.comp_id 
                    LEFT JOIN dsp_comp_staff as compy ON compy.staff_id = deli.add_staff_id  WHERE {$where}  ORDER BY deli.`add_time` DESC LIMIT $start,$limit");
         foreach($result as $key=>$value){
             $delivery_id = $value['delivery_id'];
             $result[$key]['audit_remark']="";
             $result[$key]['add_times']=date("Y-m-d H:i:s",$value['add_time']);
             $res = $this->db->stat->query("SELECT `cpm_num`,`cpc_num`,`con_rate` FROM `dsp_ad_stat` WHERE  `delivery_id`=$delivery_id");
             if($res){
                 $result[$key]['cpm_num'] = $res[0]['cpm_num'];
                 $result[$key]['cpc_num'] = $res[0]['cpc_num'];
                 $result[$key]['con_rate'] = $res[0]['con_rate'];
             } else {
                 $result[$key]['cpm_num'] = 0;
                 $result[$key]['cpc_num'] = 0;
                 $result[$key]['con_rate'] = 0;
             }
             if($value['delivery_status']==2){
                 $audit_remark = $this->db->dsp->query("SELECT audit_remark FROM dsp_delivery_audit WHERE delivery_id=$delivery_id AND audit_status=2 ORDER BY audit_time desc limit 0,1");
                 $result[$key]['audit_remark']=$audit_remark[0]['audit_remark'];
             }
         }
         return $result;
    }

    public function getDeliveryInfo($id){
        $result = $this->db->dsp->query("SELECT del.*,comp.comp_name AS comp_name,compp.comp_name AS comp_pname FROM `dsp_delivery`  AS del 
            JOIN dsp_company AS comp ON comp.comp_id= del.comp_id
            JOIN dsp_company AS compp ON compp.comp_id = del.comp_pid
            WHERE delivery_id=$id");
        $result = $result[0];
        if($result){
            $crowd_id = $result['crowd_id'];
            $crowd_info = $result['crowd_info'];
            $crowd_info = trim($crowd_info,",");
            $crowd_type = $result['crowd_type'];
            if($crowd_type==2){
                $crowd_name = $this->db->dsp->query("SELECT `crowd_name` FROM  dsp_crowd_info WHERE crowd_id=$crowd_id");
                $result['tag_name'] = "自定义人群包（".$crowd_name[0]['crowd_name'].")";
            } else {
                if($crowd_info==0){
                    $result['tag_name'] = "不限";
                } else {
                    $cowd_res = $this->db->dsp->query("SELECT tag_name FROM dsp_crowd_tag WHERE c_tag_id in ($crowd_info)");
                    foreach($cowd_res as $k=>$v){
                        $crowd_tag_name[$k] = $v['tag_name'];
                    }
                    $result['tag_name'] = implode(",",$crowd_tag_name);
                }
            }


            $material_id = $result['material_id'];
            $material_info = unserialize($result['material_info']);
            $ad_size_id = $material_info[0]['ad_size_id'];
            $material_pic = $this->db->dsp->query("SELECT ads.ad_size_name,adgroup_name FROM dsp_ad_size  AS ads
                JOIN dsp_adgroup AS adg ON adg.adgroup_id = ads.adgroup_id
                WHERE ad_size_id=$ad_size_id   LIMIT 0,1");
            $result['img_url'] = $material_info[0]['img_url'];
            $result['ad_size_name'] = $material_pic[0]['ad_size_name'];
            $result['adgroup_name'] = $material_pic[0]['adgroup_name'];
            $result['material_url'] = $material_info[0]['material_url'];
            //查询消耗金额
            $freeze_id = $result['freeze_id'];
            $deli_expend = $this->db->account->query("SELECT * FROM dsp_freeze WHERE freeze_id=$freeze_id");
            $result['consume_amount'] = $deli_expend[0]['consume_amount']/100;
            $result['return_amount'] = ($deli_expend[0]['freeze_amount']-$deli_expend[0]['consume_amount'])/100;
            $result['crowd_info'] = $crowd_info;
            return $result;

        }
    }
    /**
     * 修改投放
     */
    public function update($data,$deliveryId){
        $where['delivery_id'] =  $deliveryId;
        $result = $this->db->dsp->table("dsp_delivery")->data($data)->where($where)->update();
        return $result;
    }
    
	/**
	 * @author	skylon
	 * @desc	修改投放广告
	 * @param array() $data
	 */
	public function editAdvetising($data,$delivery_id){
		return $this->db->dsp->table( "dsp_delivery"  )->data( $data )->where("delivery_id='$delivery_id'")->update(  );
	}
	/**
	 * @author	skylon
	 * @desc	添加广告投放状态修改记录
	 * @param array() $data
	 */
    public function addAdvetisingStatus($data){
       return $this->db->dsp->table( "dsp_delivery_status_record" )->data( $data )->insert(  );
    }
    
	/**
	 * @author	skylon
	 * @desc	获取广告位的详细信息
	 */
	public function getPosInfo($ad_size_id){
		$result = $this->db->dsp->query("SELECT * FROM `dsp_ad_position`  WHERE ad_size_id='$ad_size_id' ");
        if($result){
            return $result[0];
        }
	}
	/**
	 * @author	skylon
	 * @desc	广告投放状态任务记录
	 * @param array() $data
	 */
	public function addAdvetisingStateCrontab($data){
		return $this->db->dsp->table( "dsp_delivery_status_crontab" )->data( $data )->insert(  );
	}

	/**
     * 首页统计
     */
	public function getDliveryCount($where){
	    $oneres = $this->db->dsp->query("SELECT count(*) as count  FROM dsp_delivery WHERE delivery_status=0 and {$where}");
	    $twores = $this->db->dsp->query("SELECT count(*) as count  FROM dsp_delivery WHERE delivery_status=1 and {$where}");
	    $threeres = $this->db->dsp->query("SELECT count(*) as count  FROM dsp_delivery WHERE delivery_status=3 and {$where}");
	    $compingres = $this->db->dsp->query("SELECT comp_id,count(*) AS count  FROM dsp_delivery WHERE  delivery_status=1  and {$where} GROUP BY comp_id");
	    $comphaveres = $this->db->dsp->query("SELECT comp_id,count(*) AS count  FROM dsp_delivery WHERE  {$where} GROUP BY comp_id");
	    $res['zero'] = empty($oneres)?0:$oneres[0]['count'];
        $res['one'] = empty($twores)?0:$twores[0]['count'];
        $res['three'] = empty($threeres)?0:$threeres[0]['count'];
        $res['comping'] = empty($compingres)?0:count($compingres);
        $res['comphave'] = empty($comphaveres)?0:count($comphaveres);
        return $res;

    }
    /**
     * 首页按代理商的统计
     */
    public function countByCompp($where){
    $res = $this->db->stat->query("SELECT stat_day,stat_time,cpm_num,cpc_num,con_rate FROM dsp_ad_daytime_stat WHERE {$where}");
    return $res;
    }

    /***
     * 投放时自定义人群列表
     */
    public function getCrowdlList($where){
        $result = $this->db->dsp->query("SELECT crowd_id,crowd_name FROM `dsp_crowd_info` WHERE {$where}  ORDER BY `add_time` DESC");
        return $result;
    }

    //添加冻结记录
    public function  addFreezeGdt($data){
        $freeze_record_id =$this->db->dsp->table("gdt_freeze_record")->data($data)->insert();
        return $freeze_record_id;
    }
    //修改冻结记录
    public function  updateFreezeGdt($data,$freeze_insert_id){
        $where['freeze_record_id'] = $freeze_insert_id;
        $res = $this->db->dsp->table("gdt_freeze_record")->data($data)->where($where)->update();
        return $res;
    }



}