<?php

class Plan extends Model
{
    /**
     * [addPlan 新增一条计划]
     * @param [array] $data [计划所需数据]
     */
    public function addPlan($data)
    {

        $this->db->dsp->startTrans();
        $plan_id = $this->insertPlan($data['plan']);
        //区域代理插入代理商
        if ($plan_id && isset($data['agent'])) {
            $data['agent']['plan_id'] = $plan_id;
            $agent_id = $this->insertAgent($data['agent']);
            if ($plan_id && $agent_id) {
                $this->db->dsp->commit();
                return true;
            }else{
                $this->db->dsp->rollback();
                return false;
            }
        // 全国代理直接提交
        }else {
            $this->db->dsp->commit();
            return true;
        }

    }

    /**
     * [insertAgent 插入计划代理商]
     * @param  [type] $agent_data [description]
     * @return [type]             [description]
     */
    protected function insertAgent($agent_data)
    {
        $agent_id = $this->db->dsp->table('t_plan_agents')->data($agent_data)->insert();

        return $agent_id;

    }

    /**
     * [insertPlan 插入计划信息]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function insertPlan($data)
    {
        $plan_id = $this->db->dsp->table('t_plans')->data($data)->insert();

        return $plan_id;

    }

    /**
     * [getAccountAgent 得到账户代理商]
     * @param  [int] $comp_id [账户id]
     * @return [type]          [description]
     */
    public function getAccountAgent($comp_id)
    {
        $rel = $this->db->dsp->table('dsp_company')->field('comp_id ,comp_name,sc_pid')->where('comp_id = '.$comp_id)->find();

        return $rel;

    }

    /**
     * [getScInfo 通过城市id得到城市信息]
     * @param  [type] $sc_pid [description]
     * @return [type]         [description]
     */
    public function getScInfo($sc_pid)
    {

        $where = 'sc_id = '.$sc_pid;
        $rel = $this->getScInfoInterface($where);
        return $rel[0];
    }

    /**
     * [getAllProvince 得到所有省]
     * @return [type] [description]
     */
    public function getAllProvince()
    {
        $where = 'sc_pid = 0';
        $rel = $this->getScInfoInterface($where);
        return $rel;
    }


    /**
     * [getScInfoInterface 获取城市信息接口]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getScInfoInterface($where)
    {
        $rel = $this->db->dsp->table('dsp_statecity')->field('sc_name ,sc_id')->where($where)->select();
        return $rel;
    }

    /**
     * [getAllCity 得到指定城市/区县]
     * @param  [type] $sc_id [城市/区县id]
     * @return [type]        [description]
     */
    public function getCity($sc_id)
    {
        $where = 'sc_pid = '.$sc_id;
        $rel = $this->getScInfoInterface($where);
        return $rel;

    }

    /**
     * [getAuthConfig 验证计划的的唯一性]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getAuthConfig($where)
    {
        $rel = $this->db->dsp->table('t_plans')->where($where)->find();
        return $rel;
    }

    public function checkAgentOnly($where)
    {
        $sql = "select b.plan_id from t_plan_agents as a
         join t_plans as b on a.plan_id = b.plan_id ".$where;
        $rel = $this->db->dsp->query($sql);
        return $rel;

    }


    /**
     * [checkCityPlan 检查城市中的的代理商是否有在投计划]
     * @param  [type] $City_id [城市id]
     * @return [type]          [description]
     */
    public function checkCityPlan($city_id)
    {

        $sql = "select c.plan_id from dsp_company as a join t_plan_agents as b on a.comp_id = b.agent_id
        join t_plans as c on c.plan_id = b.plan_id where a.sc_pid = ".$city_id." and c.plan_dsp_status in (1,2,3,4) ";
        $rel = $this->db->dsp->query($sql);
        return $rel;

    }

    /**
     * [getAllPlan description]
     * @param  [type] $page            [description]
     * @param  [type] $limit           [description]
     * @param  string $plan_dsp_status [description]
     * @return [type]                  [description]
     */
    public function getAllPlan($page,$limit,$plan_dsp_status = '')
    {
        $uinfo = $_SESSION['uinfo'];
        if (empty($plan_dsp_status)) {
            $where = ' and 1';
        }else {
            $where = ' and a.plan_dsp_status = '.$plan_dsp_status;
        }

        // ( select SUBSTRING_INDEX(GROUP_CONCAT(is_read order by create_at desc) ,",",1) as is_read ,plan_id from t_plan_message group by plan_id )
        $field = "b.pn_name as pn_name,
        a.plan_name as plan_name,
        a.plan_id,
        a.start_date as start_date,
        a.end_date as end_date,
        a.gaining_followers as gaining_followers,
        a.new_add_followers as new_add_followers,
        a.net_growth_followers as net_growth_followers,
        a.order_amount as order_amount,
        case a.settlement_amount when 0 then '未结算' ELSE a.settlement_amount  end as settlement_amount,
        a.plan_dsp_status as plan_dsp_status,
        a.tmp_amount_state as tmp_amount_state,
        ifnull(c.plan_message_id,0) as is_read";
        $sql = 'select '.$field.' from t_plans as a inner join t_wechat_public_number as b on a.pn_id = b.pn_id
        left join t_plan_message  as c on a.plan_id=c.plan_id and a.plan_dsp_status = c.message_state
        where b.pn_add_uid= '.$uinfo['staff_id'].$where.' group by a.plan_id limit '.$page.','.$limit;
        $rel = $this->db->dsp->query($sql);
        return $rel;

    }

    /**
     * [getPlanInfo 获取指定计划的详情]
     * @param  [type] $plan_id [description]
     * @return [type]          [description]
     */
    public function getPlanInfo($plan_id)
    {

        $join = '';
        $field = 'b.pn_name,
        b.pn_id,
        a.settlement_type,
        a.plan_name,
        a.start_date,
        a.end_date,
        a.gaining_followers,
        a.order_amount,
        a.new_add_followers,
        a.net_growth_followers,
        a.settlement_amount,
        a.payment_method,
        a.tmp_amount,
        a.tmp_amount_state,
        a.event_id,
        a.agent_type,
        a.city_id';

        $join = ' left join  t_plan_agents c on c.plan_id = a.plan_id left join dsp_statecity as d on a.city_id = d.sc_id ';
        $field = $field.' ,c.agent_id,c.agent_name';
        $field = $field.' ,ifnull(d.sc_name,0) as sc_name, d.sc_pid as sc_pid';
         $sql = 'select '.$field.' from t_plans as a inner join t_wechat_public_number as b on a.pn_id = b.pn_id
         '.$join.' where a.plan_id = '.$plan_id.' limit 0,1';
         $rel = $this->db->dsp->query($sql);
         return $rel[0];
    }


    public function getEventInfo($event_id)
    {

        $rel = $this->db->dsp->table('t_agent_events')->where('agent_event_id='.$event_id)->find();

        return $rel;

    }

    /**
     * [planCount 得到计划数量]
     * @param  string $plan_dsp_status [计划状态]
     * @return [type]                  [description]
     */
    public function planCount($plan_dsp_status = '')
    {
        $uinfo = $_SESSION['uinfo'];
        if (empty($plan_dsp_status)) {
            $where = ' and 1';
        }else {
            $where = ' and a.plan_dsp_status = '.$plan_dsp_status;
        }
        $sql = 'select count(a.plan_id) as num from t_plans as a inner join t_wechat_public_number as b on a.pn_id = b.pn_id
        where b.pn_add_uid= '.$uinfo['staff_id'].$where.' group by a.plan_id ';
        $rel = $this->db->dsp->query($sql);
        return isset($rel[0]['num'])?$rel[0]['num']:0;
    }

    /**
     * [getPlansFailureCause 得到指定计划失败原因]
     * @param  [int] $plan_id [计划id]
     * @return [type]          [description]
     */
    public function getPlansFailureCause($plan_id)
    {
        $rel = $this->db->dsp->table('t_plan_action_log')
        ->field('plans_failure_cause')
        ->where('plan_id='.$plan_id)
        ->order('create_at desc')
        ->limit('0,1')
        ->find();

        return $rel;
    }

    public function editPlan($data)
    {
        $id = $rel = $this->db->dsp->table('t_agent_events')->data($data)->insert();

        return $id;

    }

    public function applyModification($data,$plan_id)
    {
        $this->db->dsp->startTrans();
        $plan_info = $this->getAuthConfig('plan_id='.$plan_id);
        $event_id = $this->editPlan($data);
        $message_data = array('plan_id'=>$plan_id,'message'=>'修改计划已申请','create_at'=>date('Y-m-d H:i:s',time()),'message_state'=>$plan_info['plan_dsp_status']);
        $plan_message_id = $this->db->dsp->table('t_plan_message')->data($message_data)->insert();
        if ($event_id && $plan_message_id) {
            $this->db->dsp->commit();
            return true;
        }else {
            $this->db->dsp->rollback();
            return false;
        }

    }


    /**
     * [update 更新指定计划]
     * @param  [array] $data    [数据集]
     * @param  [int] $plan_id [计划id]
     * @return [type]          [description]
     */
    public function update($data , $plan_id)
    {
        $this->db->dsp->startTrans();

        // 插入代理商事件
        $event_id = $this->editPlan($data['agent_events']);
        if ($event_id) {
            // 更新计划
            $plan_id = $this->db->dsp->table('t_plans')->data($data['update_data'])->where('plan_id='.$plan_id)->update();
        }

        if ($event_id && $plan_id) {
            $this->db->dsp->commit();
            return true;
        }else {
            $this->db->dsp->rollback();
            return false;
        }
    }


    /**
     * [getPlanStateMessage 得到计划状态信息]
     * @return [type] [description]
     */
    public function getPlanStateMessage($plan_id)
    {

        $plan_info = $this->getAuthConfig('plan_id='.$plan_id);

        $rel = $this->db->dsp->table('t_plan_message')
        ->field('message,create_at,plan_message_id')
        ->where('plan_id ='.$plan_id.' and message_state='.$plan_info['plan_dsp_status'])
        ->order('create_at desc')
        ->find();
        $data = ['is_read'=>1];

        $plan_id = $this->db->dsp->table('t_plan_message')->data($data)->where('plan_message_id ='.intval($rel['plan_message_id']))->update();

        return $rel;
    }


    public function getpAymentMethod($plan_id)
    {
        $AymentMethod = $this->db->dsp->table('t_plans')
        ->where('plan_id ='.$plan_id)
        ->field('payment_method')
        ->find();

        return $AymentMethod;

    }


    public function ayment($data,$aymentMethod,$plan_id)
    {
        $this->db->dsp->startTrans();

        $plan_id = $this->db->dsp->table('t_plans')->data($data)->where('plan_id = '.$plan_id)->update();

        if ($plan_id) {

            if ($aymentMethod == 0) {

                $message_data = [
                    'plan_id'=>$plan_id,
                    'message'=>'计划已完成',
                    'create_at'=>date('Y-m-d H:i:s',time())
                ];

            }elseif ($aymentMethod == 1) {

                // 得到排队信息
                // $num = $this->getOerderInfo($plan_id);
                // if ($num == 0) {
                //     $num = 1;
                // }
                $message_data = [
                    'plan_id'=>$plan_id,
                    'message'=>'相关工作人员正在安排投放',
                    'create_at'=>date('Y-m-d H:i:s',time())
                ];
            }

            $plan_message_id = $this->db->dsp->table('t_plan_message')->data($message_data)->insert();

            if ($plan_id && $plan_message_id) {
                $this->db->dsp->commit();
                return true;
            }else {
                $this->db->dsp->rollback();
                return false;
            }

        }
    }

    /**
     * [getOerderInfo 得到排队信息]
     * @return [type] [description]
     */
    // public function getOerderInfo($plan_id)
    // {
    //     $num = $this->db->dsp->table('t_plans')
    //     ->where('plan_dsp_status=2 and plan_id='.$plan_id.' and update_at > '.date('Y-m-d H:i:s',time()))
    //     ->count();
    //
    //     return $num;
    //
    //
    // }
    //
    //
    /**
     * [stopPlan 白名单停止投放停止投放]
     * @return [type] [description]
     */
    public function whiteListStopPlan($plan_id)
    {
        // 插入代理商事件处理
        $data = [
            'plan_id'=>$plan_id,
            'message'=>'申请停止投放',
            'event_status'=>2
        ];
        $agent_event_id = $this->editPlan($data);

        // 停止投放
        // 记录几乎审核log
        // 记录状态改变log
        // 插入dsp消息


    }

    /**
     * [stopPlan 非停止投放停止投放]
     * @return [type] [description]
     */
    public function stopPlan($plan_id)
    {

        $this->db->dsp->startTrans();
        // 插入代理商事件处理
        $data = [
            'plan_id'=>$plan_id,
            'message'=>'申请停止投放',
            'event_status'=>1,
            'event_type'=>1
        ];
        $agent_event_id = $this->editPlan($data);

        // 插入dsp消息
        $message_data = array('plan_id'=>$plan_id,'message'=>'停止计划已申请','create_at'=>date('Y-m-d H:i:s',time()));
        $plan_message_id = $this->db->dsp->table('t_plan_message')->data($message_data)->insert();
        if ($plan_message_id && $agent_event_id) {
            $this->db->dsp->commit();
            return true;
        }else {
            $this->db->dsp->rollback();
            return false;
        }

    }


    /**
     * [cancelPlan 取消投放]
     * @param  [type] $plan_id [计划id]
     * @return [type]          [description]
     */
    public function cancelPlan($plan_id)
    {
        $this->db->dsp->startTrans();
        $message_data = array('plan_id'=>$plan_id,'message'=>'已完成','create_at'=>date('Y-m-d H:i:s',time()));
        $plan_message_id = $this->db->dsp->table('t_plan_message')->data($message_data)->insert();
        $plan_id = $this->db->dsp->table('t_plans')->data(['plan_dsp_status'=>5,'plan_boss_status'=>7])->where('plan_id = '.$plan_id)->update();
        if ($plan_message_id && $plan_id) {
            $this->db->dsp->commit();
            return true;
        }else {
            $this->db->dsp->rollback();
            return false;
        }

    }


    public function checkAccountPlan($plan_id)
    {
        $uinfo = $_SESSION['uinfo'];
        $sql = 'select a.plan_id from t_plans as a inner join t_wechat_public_number as b on a.pn_id = b.pn_id
        where  a.plan_id = '.$plan_id.' and b.pn_add_uid = '.$uinfo['staff_id'];
        $rel = $this->db->dsp->query($sql);
        return $rel;

    }


    public function tmpPayment($tmp_amount,$agent_event_id)
    {

        $event_rel = $this->db->dsp->table('t_agent_events')->where('agent_event_id='.$agent_event_id)->find();

        $data =[
            'settlement_amount'=>$tmp_amount,
            'tmp_amount_state'=>2,
            'plan_name'=>$event_rel['change_plan_name'],
            'gaining_followers'=>$event_rel['change_gaining_followers'],
            'end_date'=>$event_rel['change_end_date'],
            'tmp_amount'=>0
        ];

        $plan_id = $this->db->dsp->table('t_plans')->data($data)->where('plan_id='.$event_rel['plan_id'])->update();

        return $plan_id;

    }


}
