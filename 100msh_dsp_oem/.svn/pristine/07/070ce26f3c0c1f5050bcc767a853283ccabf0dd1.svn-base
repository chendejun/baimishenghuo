<?php

/**
 *
 */
class Scenes extends Model
{

    private $host = 'dmp-api-dev.100msh.com';
    private $appid = 'bmzgappsys';
    private $secret = 'af22b59f6414910748726ca58dcf9fa9';
    private $updateSceneHost = '/api/updateSceneInfo.do';



    /**
     * [getToken 获取token]
     * @return [type] [description]
     */
    public function getToken()
    {
        $token = '';
        $token = $this->redis->get('token_'.date('Ymd',time()));
        if (empty($token)) {
            $url = $this->host.'/author/getAccessToken.do?appid='.$this->appid.'&secret='.$this->secret;
            $token_str = DoNetwork::makeRequest($url);
            if (isset(json_decode($token_str)->token) && !empty(json_decode($token_str)->token)) {
                $token = json_decode($token_str)->token;
                $this->redis->set('token_'.date('Ymd',time()),$token,60*60*24);
            }

        }
        return $token;
    }

    public function getMacStr($mac_arr)
    {
        //var_dump($mac_arr);exit;
        $mac_str = '';
        $len = count($mac_arr);
        if ($len>0) {
            foreach ($mac_arr as $key => $value) {
                if (is_array($value)) {

                    if ($key == $len-1) {
                        $mac_str .= $value['mac_addr'];
                    }else {
                        $mac_str .= $value['mac_addr'].',';
                    }

                }else {
                    if ($key == $len-1) {
                        $mac_str .= $value;
                    }else {
                        $mac_str .= $value.',';
                    }
                }

            }
        }
        return $mac_str;
    }

    public function updateScenesConfig($scene_id,$mac_str,$detection_radius,$stop_time,$industry_id,$baidu_id = 0)
    {

        $token = $this->getToken();
        $signal = (int)$detection_radius + 55;
        $url = $this->host.$this->updateSceneHost.'?token='.$token.'&scene_id='.$scene_id.'&route_macs='.$mac_str.'&stay_length='.$stop_time.'&rssi='.$signal.'&industry_id='.$industry_id.'&baidu_id='.$baidu_id;
         //var_dump($url);
        $data = DoNetwork::makeRequest($url,'POST');
        $data_obj = json_decode($data);
        //var_dump($data_obj);exit;
        if ($data_obj->status == 1 && isset($data_obj->status)) {
            $data = [
                'push_state'=>2
            ];
        }else {
            $data = [
                'push_state'=>1
            ];
        }
        $rel_scenes = $this->db->dsp->table('dsp_scenes')->data($data)->where('scene_id='.$scene_id)->update();
    }

    /**
     * [getAgentAdvertiser 得到代理商广告主]
     * @param  [int] $comp_id [代理商id]
     * @return [type]          [description]
     */
    public function getAgentAdvertiser($comp_id)
    {
        $field = 'a.comp_id,
        a.comp_name,
        ifnull(b.scene_id,0) as scene_id
        ';
        $sql = ' select '.$field.' from dsp_company as a
        left join dsp_scenes as b on a.comp_id = b.comp_id and b.scenes_state != 3
        where comp_type = 1 and a.comp_pid = '.$comp_id;
        $rel = $this->db->dsp->query($sql);

        return  $rel;
    }

    /**
     * [getAdvertiserInfo 得到广告主信息]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getAdvertiserInfo($where)
    {
        $rel = $this->db->dsp->table('dsp_company')->where($where)->find();

        return $rel;
    }

    /**
     * [getAdvertiserMac 得到广告主设备]
     * @param  [type] $comp_id [广告主id]
     * @return [type]          [description]
     */
    public function getAdvertiserMac($comp_id)
    {
        $where = ' comp_id = '.$comp_id.' and bind_status = 1 ';
        $field = ' mac_id,mac_addr,mac_set,mac_status';
        $rel = $this->db->dsp->table('dsp_mac_list')->field($field)->where($where)->select();
        return $rel;
    }

    /**
     * [getSceneMac 得到场景设备]
     * @param  [type] $scene_id [description]
     * @return [type]           [description]
     */
    public function getSceneMac($scene_id)
    {
        //$where = 'scene_id='.$scene_id;
        $field = 'b.mac_addr,b.mac_status,b.mac_set,b.mac_id';
        $sql = ' select '.$field.' from dsp_scenes_mac as a inner join dsp_mac_list as b on a.mac_id = b.mac_id
        where a.scene_id='.$scene_id;
        $rel = $this->db->dsp->query($sql);
        return $rel;
    }

    /**
     * [getAdvertiserScene 得到广告主场景]
     * @param  [type] $comp_id [广告主id]
     * @return [type]          [description]
     */
    public function getAdvertiserScene($comp_id)
    {
        $rel = $this->db->dsp->table('dsp_scenes')->where('comp_id='.$comp_id.' and scenes_state != 3 ')->find();
        return $rel;
    }

    /**
     * [getAdvertiserScene 得到场景信息]
     * @param  [type] $scene_id [场景id]
     * @return [type]          [description]
     */
    public function getSceneInfo($scene_id)
    {

        $rel = $this->db->dsp->table('dsp_scenes')->where('scene_id='.$scene_id)->find();
        return $rel;
    }



    /**
     * [getMobilePhone 得到服务商手机号码]
     * @param  [type] $comp_id [服务商id]
     * @return [type]          [description]
     */
    public function getMobilePhone($comp_id)
    {
        $where = 'comp_id='.$comp_id.' and comp_type = 2';
        $rel = $this->getAdvertiserInfo($where);
        return $rel['mobile_phone'];
    }

    /**
     * [generatingRechargeRecord 生成充值记录]
     * @param  [type] $comp_pid [服务商id]
     * @param  [type] $comp_id  [广告主id]
     * @return [type]           [description]
     */
    public function generatingRechargeRecord($comp_pid,$comp_id,$scene_type = 2)
    {
        $data = [
            'comp_id'=>$comp_id,
            'comp_pid'=>$comp_pid,
            'state'=>1,
            'scene_type'=>$scene_type
        ];
        $id = $this->db->dsp->table('dsp_advertiser_recharge_record')->data($data)->insert();
        return $id;
    }

    /**
     * [updateRechargeRecord 更新支付记录]
     * @param  [type] $re_record_id  [主键]
     * @param  [type] $serial_number [流水号]
     * @return [type]                [description]
     */
    public function updateRechargeRecord($re_record_id,$serial_number,$amount)
    {
        $where = 're_record_id = '.$re_record_id;
        $data = [
            'state'=>2,
            'serial_number'=>$serial_number,
            'amount'=>$amount
        ];
        $rel = $this->db->dsp->table('dsp_advertiser_recharge_record')->data($data)->where($where)->update();
        return $rel;
    }

    /**
     * [advertiserPaymentInfo 得到广告主支付信息]
     * @param  [type] $comp_id [description]
     * @return [type]          [description]
     */
    public function advertiserPaymentInfo($comp_id)
    {
        $where = 'comp_id='.$comp_id;
        $rel = $this->db->dsp->table('dsp_advertiser_recharge_record')->where($where)->find();
        return $rel;
    }

    public function upgradeToApp($scene_id,$transfer_number)
    {

        $src_scenes_info = $this->getSceneInfo($scene_id);
        $this->db->dsp->startTrans();

        $record_no = 'CJ'.date('YmdHis',time()).'1'.rand(1000,9999);
        $data = [
            'scenes_type'=>3
        ];
        $rel = $this->db->dsp->table('dsp_scenes')->data($data)->where('scene_id='.$scene_id)->update();
        // 更新场景记录表
        $record_data = [
            'scenes_id'=>$scene_id,
            'scenes_type'=>3,
            'baidu_state'=>3,
            'record_no'=>$record_no,
            'apply_time'=>date('Y-m-d H:i:s',time())
        ];
        $scenes_record_id = $this->db->dsp->table('dsp_scenes_record')->data($record_data)->insert();
        $uinfo = $_SESSION['uinfo'];
        $staff_id = $uinfo['staff_id'];

        $data = [
            'record_no'=>$record_no,
            'comp_pid'=>$uinfo['comp_id'],
            'comp_id'=>$src_scenes_info['comp_id'],
            'poster_type'=>3,
            'record_type'=>2,
            'record_status'=>1,
            'record_num'=>$transfer_number,
            'staff_id'=>$staff_id,
            'add_time'=>time()
        ];
        $record_rel = $this->recordTransferCreate($data);
        $res = $this->getServerSceneResource($uinfo['comp_id']);
        $resource_data = [
            'app_num'=>$res['app_num'] - $transfer_number,
        ];
        $res_rel = $this->resourceTransferUpdate($uinfo['comp_id'],$resource_data);
        if (!$record_rel || !$res_rel ) {
            $this->db->dsp->rollback();
            return false;
        }
        if ($rel && $scenes_record_id) {
            $this->db->dsp->commit();
            return true;
        }else {
            $this->db->dsp->rollback();
            return false;
        }

    }

    public function saveScenes($scene_date,$mac_date,$transfer_number)
    {
        $this->db->dsp->startTrans();
        // 获取所需设备字符
        $mac_str = $this->getMacStr($mac_date);

        $id = $this->db->dsp->table('dsp_scenes')->data($scene_date)->insert();
        $mac_addr_arr = array();
        // 场景插入成功插入设备
        if ($id) {
            foreach ($mac_date as $key => $value) {
                $mac_id = 0;
                $date = [
                    'scene_id'=>$id,
                    'mac_id'=>$value
                ];
                $mac_id = $this->db->dsp->table('dsp_scenes_mac')->data($date)->insert();
                $field = 'mac_addr';
                //var_dump($value);exit;
                $rel = $this->db->dsp->table('dsp_mac_list')->field($field)->where('mac_id='.$value)->find();
                //var_dump($rel);
                $mac_addr_arr[] = $rel['mac_addr'];
                // 插入设备失败回滚
                if (!$mac_id || !$rel) {
                    $this->db->dsp->rollback();
                    return false;
                }
            }
        }
        // 不是草稿还需要插入场景记录表和调用百万点推动接口
        if ($scene_date['scenes_state'] != 1) {
            // 基础版本为待申请
            if ($scene_date['scenes_type'] == 1) {
                $baidu_state = 1;
            // 其他版本为待开通
            }else{
                $baidu_state = 3;
            }
            $record_data = [
                'scenes_id'=>$id,
                'scenes_type'=>$scene_date['scenes_type'],
                'baidu_state'=>$baidu_state,
                'apply_time'=>date('Y-m-d H:i:s',time())
            ];
            $scenes_record_id = $this->db->dsp->table('dsp_scenes_record')->data($record_data)->insert();

            // 大数据推送接口
            $mac_str = $this->getMacStr($mac_addr_arr);
            $this->updateScenesConfig($id,$mac_str,$scene_date['detection_radius'],$scene_date['stop_time'],$scene_date['industry_id']);
            if (empty($scenes_record_id)) {
                $this->db->dsp->rollback();
                return false;
            }
            // 高级版资源转移
            if ($scene_date['scenes_type'] == 2 || $scene_date['scenes_type'] == 3) {
                $uinfo = $_SESSION['uinfo'];
                $staff_id = $uinfo['staff_id'];
                $record_no = 'CJ'.date('YmdHis',time()).'1'.rand(1000,9999);
                $data = [
                    'record_no'=>$record_no,
                    'comp_pid'=>$uinfo['comp_id'],
                    'comp_id'=>$scene_date['comp_id'],
                    'poster_type'=>$scene_date['scenes_type'],
                    'record_type'=>2,
                    'record_status'=>1,
                    'record_num'=>$transfer_number,
                    'staff_id'=>$staff_id,
                    'add_time'=>time()
                ];
                $record_rel = $this->recordTransferCreate($data);
                $res = $this->getServerSceneResource($uinfo['comp_id']);
                $resource_data = [
                    'high_num'=>$res['high_num'] - $transfer_number,
                ];
                $res_rel = $this->resourceTransferUpdate($uinfo['comp_id'],$resource_data);
                // 更新流水号
                $scenes_record = $this->db->dsp->table('dsp_scenes_record')->data(['record_no'=>$record_no])->where('scenes_record_id='.$scenes_record_id)->update();
                if (!$record_rel || !$res_rel || !$scenes_record) {
                    $this->db->dsp->rollback();
                    return false;
                }
            }
        }
        if (empty($id)) {
            $this->db->dsp->rollback();
            return false;
        }
        $this->db->dsp->commit();
        return true;
    }

    /**
     * [updateScenes 更新场景]
     * @param  [type] $scene_date [更新场景信息]
     * @param  [type] $scene_id   [场景id]
     * @return [type]             [description]
     */
    public function updateScenes($scene_date,$scene_id,$transfer_number)
    {
        $src_scenes_info = $this->getSceneInfo($scene_id);
        $this->db->dsp->startTrans();

        // 不是保存到草稿，且升级到高级版，还需插入记录表
        // 原状态为草稿，更新到场景,or 原状态为非草稿，且为普通版，且升级为高级版。
        // ($src_scenes_info['scenes_state'] == 1 && $scene_date['scenes_state'] == 2) || ($src_scenes_info['scenes_state'] == 2 && $src_scenes_info['scenes_type'] == 1 && $scene_date['scenes_type'] == 2)
        if (($src_scenes_info['scenes_state'] == 1 && $scene_date['scenes_state'] == 2) || ($src_scenes_info['scenes_state'] == 2 && $src_scenes_info['scenes_type'] == 1 && $scene_date['scenes_type'] == 2)) {
            if ($scene_date['scenes_type'] != 1 ) {
                $baidu_state = 3;
            }else {
                $baidu_state = 1;
            }
            $record_data = [
                'scenes_id'=>$scene_id,
                'scenes_type'=>$scene_date['scenes_type'],
                'baidu_state'=>$baidu_state,
                'apply_time'=>date('Y-m-d H:i:s',time())
            ];
            $scenes_record_id = $this->db->dsp->table('dsp_scenes_record')->data($record_data)->insert();
            // 升级到高级版需要支付
            if ($scene_date['scenes_type'] == 2) {
                $uinfo = $_SESSION['uinfo'];
                $staff_id = $uinfo['staff_id'];
                $record_no = 'CJ'.date('YmdHis',time()).'1'.rand(1000,9999);
                $data = [
                    'record_no'=>$record_no,
                    'comp_pid'=>$uinfo['comp_id'],
                    'comp_id'=>$src_scenes_info['comp_id'],
                    'poster_type'=>$scene_date['scenes_type'],
                    'record_type'=>2,
                    'record_status'=>1,
                    'record_num'=>$transfer_number,
                    'staff_id'=>$staff_id,
                    'add_time'=>time()
                ];
                $record_rel = $this->recordTransferCreate($data);
                $res = $this->getServerSceneResource($uinfo['comp_id']);
                $resource_data = [
                    'high_num'=>$res['high_num'] - $transfer_number,
                ];
                $res_rel = $this->resourceTransferUpdate($uinfo['comp_id'],$resource_data);
                // 更新流水号
                $scenes_record = $this->db->dsp->table('dsp_scenes_record')->data(['record_no'=>$record_no])->where('scenes_record_id='.$scenes_record_id)->update();
                if (!$record_rel || !$res_rel || !$scenes_record) {
                    $this->db->dsp->rollback();
                    return false;
                }
            }
            if (empty($scenes_record_id)) {
                $this->db->dsp->rollback();
                return false;
            }
        }
        // 更新场景设备
        if (isset($scene_date['is_auto_update']) && !empty($scene_date['is_auto_update']) && $scene_date['is_auto_update'] == 2) {
            $this->updateSceneMac($scene_id,$src_scenes_info['comp_id']);
        }
        // 更新为非草稿
        if ($scene_date['scenes_state'] != 1) {
            $mac_addr_arr = $this->getSceneMac($scene_id);
            $mac_str = $this->getMacStr($mac_addr_arr);
            // 原场景为草稿,更新大数据接口
            if ($src_scenes_info['scenes_state'] == 1) {
                $this->updateScenesConfig($scene_id,$mac_str,$scene_date['detection_radius'],$scene_date['stop_time'],$scene_date['industry_id'],$src_scenes_info['baidu_code']);
            // 原场景为非草稿，改变行业需要更新大数据接口
            }else if($src_scenes_info['industry_id'] != $scene_date['industry_id']){

                $this->updateScenesConfig($scene_id,$mac_str,$src_scenes_info['detection_radius'],$src_scenes_info['stop_time'],$scene_date['industry_id'],$src_scenes_info['baidu_code']);
            }
        }
        $rel_scenes = $this->db->dsp->table('dsp_scenes')->data($scene_date)->where('scene_id='.$scene_id)->update();
        if (empty($rel_scenes)) {
            $this->db->dsp->rollback();
            return false;
        }
        $this->db->dsp->commit();
        return true;
    }

    /**
     * [recordTransferCreate 记录创建]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function recordTransferCreate($data)
    {
        $rel = $this->db->dsp->table('dsp_poster_record')->data($data)->insert();
        return $rel;
    }

    /**
     * [getServerSceneResource 得到服务商场景资源]
     * @param  [type] $comp_id [服务商id]
     * @return [type]          [description]
     */
    public function getServerSceneResource($comp_id)
    {
        $rel = $this->db->dsp->table('dsp_comp_poster')->field('high_num,app_num')->where('comp_id='.$comp_id)->find();

        return $rel;
    }

    /**
     * [resourceTransferUpdate 资源转移更新]
     * @param  [type] $comp_id [description]
     * @param  [type] $data    [description]
     * @return [type]          [description]
     */
    protected function resourceTransferUpdate($comp_id,$data)
    {

        $rel = $this->db->dsp->table('dsp_comp_poster')->where('comp_id='.$comp_id)->data($data)->update();
        return $rel;

    }


    public function updateSceneMac($scene_id,$comp_id)
    {
        // 得到广告主mac
        $advertiser_mac = $this->getAdvertiserMac($comp_id);

        // 删除原场景mac
        $mac_id = $this->db->dsp->table('dsp_scenes_mac')->where('scene_id='.$scene_id)->delete();

        foreach ($advertiser_mac as $key => $value) {
            $data = [
                'scene_id'=>$scene_id,
                'mac_id'=>$value['mac_id']
            ];
            $id = $mac_id = $this->db->dsp->table('dsp_scenes_mac')->data($data)->insert();
            if (empty($id)) {
                $this->db->dsp->rollback();
                return false;
            }
        }
    }

    /**
     * [getAppointScene 获取指定场景]
     * @param  [type] $scene_id [description]
     * @return [type]           [description]
     */
    public function getAppointScene($scene_id)
    {
        $info = $this->getSceneInfo($scene_id);
        $advertiser_info = $this->getAdvertiserInfo('comp_id='.$info['comp_id']);
        $mac_info = $this->getSceneMac($scene_id);
        $info['comp_name'] = $advertiser_info['comp_name'];
        $info['mac_list'] = $mac_info;
        return $info;
    }


    public function sceneList($limit = '',$where)
    {

        $field = '(case when a.baidu_code = "" and scenes_state = 2 and scenes_type != 1 then 0 else 1 end ) as baidu_state,a.scene_id,a.longitude,a.latitude,a.industry_id,a.scene_name,a.comp_id,a.create_time,a.scenes_state ,a.scenes_type ,b.comp_name,count(c.scene_mac_id) as mac_num ';
        $sql = ' select '.$field.' from dsp_scenes as a left join dsp_company as b on a.comp_id = b.comp_id
        left join dsp_scenes_mac as c on a.scene_id  = c.scene_id '.$where.' group by a.scene_id order by a.create_time desc  '.$limit;
        $rel = $this->db->dsp->query($sql);
        return $rel;

    }



    /**************************************************************   人群画像v2 ****************************************/

    /**
     * [getServerAvailableAmount=》getServerAvailableResources 得到服务商可用资源]
     * @param  [int] $comp_id [服务商id]
     * @return [type]          [description]
     */
    public function getServerAvailableResources($comp_id)
    {
        $rel = $this->db->dsp->table('dsp_comp_poster')->where('comp_id='.$comp_id)->find();
        return $rel;
    }

}
