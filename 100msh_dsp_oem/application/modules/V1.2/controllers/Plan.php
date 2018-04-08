<?php

/**
 * 吸粉计划
 */
class PlanController extends BasicController
{

    /**
    * [create 新建计划]
    * @return [array] [包含执行结果的数组]
    */
    public function createAction()
    {

        if (isset($_POST) && !empty($_POST)) {

            $uinfo = $_SESSION['uinfo'];
            $agent_type = $this->checkAgentTypeApi();

            // 得到表单数据
            $plan_name = Helper::clean($this->getRequest()->getPost('plan_name'));
            $pn_id = intval(Helper::clean($this->getRequest()->getPost('pn_id')));
            $settlement_type = intval(Helper::clean($this->getRequest()->getPost('settlement_type')));
            $start_date = Helper::clean($this->getRequest()->getPost('start_date'));
            $end_date = Helper::clean($this->getRequest()->getPost('end_date'));
            $agency_company = Helper::clean($this->getRequest()->getPost('agency_company'));
            $gaining_followers = intval(Helper::clean($this->getRequest()->getPost('gaining_followers')));
            $sc_id = intval(Helper::clean($this->getRequest()->getPost('sc_id')));

            // 表单验证
            if (empty($plan_name)){
            	Helper::outJson(array('state' => 0 , 'msg' => '请输入计划名称!'));
            }

            if (empty($pn_id)){
            	Helper::outJson(array('state' => 0 , 'msg' => '请选择公众号!'));
            }

            if (empty($settlement_type)){
            	Helper::outJson(array('state' => 0 , 'msg' => '请选择结算类型!'));
            }

            if (empty($start_date) || empty($end_date)){
            	Helper::outJson(array('state' => 0 , 'msg' => '请选择投放日期!'));
            }

            if (empty($agency_company) && $agent_type == 2){
            	Helper::outJson(array('state' => 0 , 'msg' => '请选择代理公司!'));
            }

            if (empty($agency_company)){
            	Helper::outJson(array('state' => 0 , 'msg' => '请输入计划增预期增加的粉丝!'));
            }

            if (empty($sc_id) && $agent_type == 1){
            	Helper::outJson(array('state' => 0 , 'msg' => '请设置代理城市!'));
            }

            // 成所需模型实例化
            $official_accounts = Helper::M('OfficialAccounts');
            $Plan = Helper::M('Plan');

            //验证公众号是否存在
            $result_pn_id = $official_accounts->selectOfficialAccounts('pn_id='.$pn_id.' and pn_add_uid='.$uinfo['staff_id']);
            if (!$result_pn_id) {
                Helper::outJson(array('state' => 0 , 'msg' => '您没有绑定该公众号!'));
            }

            // 验证结算类型的合法性
            if (!in_array($settlement_type,array(1,2))) {
                Helper::outJson(array('state' => 0 , 'msg' => '结算类型不合法!'));
            }

            // 验证投放日期的合法性
            if (!($this->checkDateTime($start_date) && $this->checkDateTime($end_date) && strtotime($start_date)<=strtotime($end_date))) {
                Helper::outJson(array('state' => 0 , 'msg' => '投放日期不合法!'));
            }

            // 验证粉丝数的合法性
            if (!is_int($gaining_followers) || $gaining_followers <= 0) {
                Helper::outJson(array('state' => 0 , 'msg' => '增加粉丝不合法!'));
            }



            // 计算出参考金额
            $order_amount = $this->getReferenceAmount($gaining_followers,$settlement_type);

            // 非白名单计划状态
            $new_data = [
                'plan_dsp_status'=>1,
                'plan_boss_status'=>1
            ];

            //白名单计划状态
            if ($official_accounts->is_whitelist($pn_id)){
                $new_data = [
                    'plan_dsp_status'=>2,
                    'plan_boss_status'=>4
                ];
            }

            $data = [
                'plan_name'=>$plan_name,
                'pn_id'=>$pn_id,
                'settlement_type'=>$settlement_type,
                'gaining_followers'=>$gaining_followers,
                'order_amount'=>$order_amount,
                'start_date'=>$start_date,
                'end_date'=>$end_date,
                'agent_type'=>0,
                'is_settlement'=>0,
            ];

            $data_insert = array();

            // 全国代理
            if (!empty($sc_id) && $agent_type == 1) {
                // 检查该城市是否有投放计划
                $where = 'agent_type=1 and city_id='.$sc_id.' and plan_dsp_status in (1,2,3,4)';
                if ($Plan->getAuthConfig($where)) {
                    Helper::outJson(array('state' => 0 , 'msg' => '该城市已经有在投计划!'));
                }

                $data['agent_type'] = 1;
                $data['city_id'] = $sc_id;
            }
            // 区域代理
            if ( $agent_type == 2) {

                // 检查公众号的唯一性
                $where = 'pn_id = '.$pn_id.' and plan_dsp_status in (1,2,3,4)';
                if ($Plan->getAuthConfig($where)) {
                    Helper::outJson(array('state' => 0 , 'msg' => '该公众号已经有在投计划!'));
                }
                // 得到账户代理商信息
                $rel = $Plan->getAccountAgent($uinfo['comp_id']);
                // 检查代理商的唯一性
                // $where = "where a.agent_id = ".$rel['data']['comp_id'].
                // " and b.start_date <= ".$end_date." and b.end_date >= ".$start_date." and b.plan_dsp_status in (1,2,3,4)";
                $where = " where a.agent_name = '".$agency_company."' and b.plan_dsp_status in (1,2,3,4)";
                if ($Plan->checkAgentOnly($where)) {
                    Helper::outJson(array('state' => 0 , 'msg' => '该代理商已经有在投计划!'));
                }

                $data['agent_type'] = 2;

                $data_insert['agent'] = [
                    'plan_id'=>0,
                    'agent_id'=>$rel['comp_id'],
                    'agent_name'=>$rel['comp_name']
                ];

            }

            $data_insert['plan'] = array_merge($data,$new_data);

            // 插入数据库
            $plan_id = $Plan->addPlan($data_insert);
            if ($plan_id) {
                Helper::outJson(array('state'=>1,'msg'=>"投放计划成功"));
            }else{
                Helper::outJson(array('state'=>0,'msg'=>"投放计划失败"));
            }
        }else {
            Helper::outJson(array('state'=>0,'msg'=>"非法请求"));
        }
    }

    /**
     * [getAccountAgentAction 得账户到代理商]
     * @return [type] [description]
     */
    public function getAccountAgentAction()
    {
        $uinfo = $_SESSION['uinfo'];
        $plan = Helper::M('Plan');
        $rel = $plan->getAccountAgent($uinfo['comp_id']);
        Helper::outJson(array('state'=>1,'data'=>$rel));
    }


    /**
     * [indexAction 计划列表]
     * @param  [type] $agency_company [description]
     * @return [type]                 [description]
     */
    public function indexAction()
    {
        $uinfo = $_SESSION['uinfo'];
        $page = Helper::clean($this->getRequest()->getParam('page',1));
        $pagecount = Helper::clean($this->getRequest()->getParam('pagecount',10));
        $plan_dsp_status = Helper::clean($this->getRequest()->getParam('plan_dsp_status',''));
        $start=($page-1)*$pagecount;
        $plan = Helper::M('Plan');
        $data = $plan->getAllPlan($start,$pagecount,$plan_dsp_status);
        $num = $this->getCount($plan_dsp_status);


        Helper::outJson(array('state'=>1,'data'=>$data,'totalcount'=>$num,'pageindex'=>$page,'pagecount'=>$pagecount));

    }

    /**
     * [getCount 得到计划数量]
     * @return [type] [description]
     */
    protected function getCount($plan_dsp_status)
    {
        $plan = Helper::M('Plan');
        $num = $plan->planCount($plan_dsp_status);
        return $num;

    }


    public function testAction()
    {

$uinfo = $_SESSION['uinfo'];
        var_dump($uinfo);


    }
    public function indexpageAction(){}
    public function createpageAction(){}
    public function editpageAction(){}

    /**
     * [checkAgentType 检查代理商类型对外接口]
     * @return [type] [description]
     */
    public function checkAgentTypeAction()
    {
        if ($this->checkAgentTypeApi() == 1) {
            // 全国代理
            Helper::outJson(array('state' => 1 , 'msg' => '全国代理!' ,'agent_type'=>1));
        }elseif ($this->checkAgentTypeApi() == 2) {
            # 区域代理
            Helper::outJson(array('state' => 1 , 'msg' => '区域代理!' ,'agent_type'=>2));
        }
    }

    protected function checkAgentTypeApi()
    {
        $uinfo = $_SESSION['uinfo'];
        $config = Yaf_Registry::get("config");
        $config_arr = explode(',', $config['dsp.whole']);

        if (in_array($uinfo['staff_id'],$config_arr)) {
            // 全国代理
            return 1;
        }else {
            # 区域代理
            return 2;
        }
    }

    /**
     * [checkDateTime 验证时间的合法性]
     * @param  [string] $date [日期字符串]
     * @return [boll]       []
     */
    public function checkDateTime($date)
    {
        //匹配时间格式为2016-02-16或2016-02-16 23:59:59前面为0时可以不写
        $patten = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/";
        if (preg_match($patten,$date)) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * [referenceAmountApiAction 对外提供得到参考金额接口]
     * @return [type] [description]
     */
    public function referenceAmountApiAction()
    {

        $gaining_followers = intval(Helper::clean($this->getRequest()->getParam('gaining_followers')));

        $sc_id = Helper::clean($this->getRequest()->getParam('sc_id',''));


        $settlement_type = Helper::clean($this->getRequest()->getParam('settlement_type'));

        if (empty($gaining_followers)){
            Helper::outJson(array('state' => 0 , 'msg' => '请设置粉丝数!'));
        }
        if (empty($settlement_type)){
            Helper::outJson(array('state' => 0 , 'msg' => '请设置结算类型!'));
        }

        $reference_amount = $this->getReferenceAmount($gaining_followers,$settlement_type,$sc_id);

        Helper::outJson(array('state' => 1 , 'data' => $reference_amount));

    }

    /**
     * [referenceAmountAction 得到参考金额]
     * @param  [int] $gaining_followers [粉丝数]
     * @param  [string] $agency_company [城市名称]
     * @param  [string] $agency_company [结算类型]
     * @return [type]                 [description]
     */
    protected function getReferenceAmount($gaining_followers,$settlement_type,$sc_id='')
    {

        if (!is_int($gaining_followers) || $gaining_followers <= 0) {
            Helper::outJson(array('state' => 0 , 'msg' => '粉丝数必须是非负整数!'));
        }
        $plan = Helper::M('Plan');
        if (empty($sc_id)) {
            $uinfo = $_SESSION['uinfo'];
            $rel = $plan->getAccountAgent($uinfo['comp_id']);
            $sc_name = $plan->getScInfo($rel['sc_pid'])['sc_name'];
        }else{
            $sc_name = $plan->getScInfo($sc_id)['sc_name'];
        }

        // 北上广深
        $first_tier = ['北京市','上海市','广州','深圳'];
        // 省会城市（不包括北上广深）
        $provincial_capital = ['天津市','重庆市','石家庄','郑州','武汉','长沙','南京','南昌','沈阳','长春','哈尔滨',
        '西安','太原','济南','成都','西宁','合肥','海口','广州','贵阳','杭州','福州','台北','兰州','昆明','拉萨',
        '银川','南宁','乌鲁木齐','呼和浩特','香港','澳门'
        ];

        $unit_price = 0;

        // 净增粉丝
        if ($settlement_type == 1) {
            $unit_price = 2.0;
            if (in_array($sc_name ,$first_tier)) {
                $unit_price = 3;
            }
            if (in_array($sc_name ,$provincial_capital)) {
                $unit_price = 2.5;
            }
        // 新增粉丝
        }elseif ($settlement_type == 2) {
            $unit_price = 1.5;
            if (in_array($sc_name ,$first_tier)) {
                $unit_price = 2.0;
            }
            if (in_array($sc_name ,$provincial_capital)) {
                $unit_price = 1.7;
            }

        }

        $reference_amount = $unit_price * $gaining_followers;

        return $reference_amount;
    }


    public function getGeographyInfoAction()
    {
        $sc_id = intval(Helper::clean($this->getRequest()->getParam('sc_id',0)));

        if ($sc_id == 0) {
            $rel = $this->getAllProvince();
        }else{
            $rel = $this->getCity($sc_id);
        }

        Helper::outJson(array('state' => 1 , 'data' => $rel));
    }


    /**
     * [getAllCity 得到指定城市/区县]
     * @param  [type] $Province [省id]
     * @return [array]           [城市结果集]
     */
    protected function getCity($sc_id)
    {

        $plan = Helper::M('Plan');
        $rel = $plan->getCity($sc_id);

        return $rel;

    }

    /**
     * [getAllProvince 得到所有省]
     * @return [type] [description]
     */
    protected function getAllProvince()
    {

        $plan = Helper::M('Plan');
        $rel = $plan->getAllProvince();

        return $rel;

    }

    /**
     * [checkCityPlanAction 检查城市中的代理商是否有在投计划]
     * @return [type] [description]
     */
    public function checkCityPlanAction()
    {

        $sc_id = intval(Helper::clean($this->getRequest()->getParam('sc_id')));
        if (empty($sc_id)) {
            Helper::outJson(array('state' => 0 , 'msg' => '没有设置城市id!'));
        }
        $plan = Helper::M('Plan');
        $rel = $plan->checkCityPlan($sc_id);
        if (count($rel)>0) {
            Helper::outJson(array('state' => 'message' , 'msg' => '注意：该城市有代理商存在在投计划，该计划将无法在已有计划下的代理商中投放!'));
        }else{
            Helper::outJson(array('state' => 1 , 'msg' =>'' ));

        }

    }

    /**
     * [getPlanInfo 得到指定计划详情]
     * @return [type] [description]
     */
    public function getPlanInfoAction()
    {

        $plan_id = intval(Helper::clean($this->getRequest()->getParam('plan_id')));
        if (empty($plan_id)) {
            Helper::outJson(array('state' => 0 , 'msg' =>'计划id未指定' ));
        }
        $plan = Helper::M('Plan');
        $data = $plan->getPlanInfo($plan_id);
        // 判断代理商类型
        if ($data['agent_type'] == 1) {
            unset($data['agent_id']);
            unset($data['agent_name']);
            $Amount_data = $this->getReferenceAmount(intval($data['gaining_followers']),$data['settlement_type'],$data['city_id']);
        }elseif ($data['agent_type'] == 2) {
            unset($data['city_id']);
            unset($data['sc_name']);
            unset($data['sc_pid']);
            $Amount_data = $this->getReferenceAmount(intval($data['gaining_followers']),$data['settlement_type']);
        }
        unset($data['new_add_followers']);
        unset($data['net_growth_followers']);
        unset($data['settlement_amount']);
        unset($data['tmp_amount']);
        unset($data['tmp_amount_state']);
        unset($data['event_id']);

        $data['order_amount'] = $Amount_data;

        Helper::outJson(array('state' => 1 , 'data' => $data));

    }


    /**
     * [getPlansFailureCauseAction 得到指定计划审核失败的原因]
     * @return [type] [description]
     */
    public function getPlansFailureCauseAction()
    {
        $plan_id = intval(Helper::clean($this->getRequest()->getParam('plan_id')));
        if (empty($plan_id)) {
            Helper::outJson(array('state' => 0 , 'msg' =>'计划id未指定' ));
        }
        $plan = Helper::M('Plan');
        $data = $plan->getPlansFailureCause($plan_id);
        Helper::outJson(array('state' => 1 , 'data' => $data));

    }

    /**
     * [editAction 修改计划]
     * @return [type] [description]
     */
    public function editAction()
    {

        if (isset($_POST) && !empty($_POST)) {
            $uinfo = $_SESSION['uinfo'];
            // 得到表单数据
            $plan_id = intval(Helper::clean($this->getRequest()->getPOST('plan_id')));
            $plan_name = Helper::clean($this->getRequest()->getPost('plan_name'));
            $pn_id = intval(Helper::clean($this->getRequest()->getPost('pn_id')));
            $settlement_type = intval(Helper::clean($this->getRequest()->getPost('settlement_type')));
            $start_date = Helper::clean($this->getRequest()->getPost('start_date'));
            $end_date = Helper::clean($this->getRequest()->getPost('end_date'));
            $gaining_followers = intval(Helper::clean($this->getRequest()->getPost('gaining_followers')));


            // 表单验证
            if (empty($plan_name)){
                Helper::outJson(array('state' => 0 , 'msg' => '请输入计划名称!'));
            }

            if (empty($pn_id)){
                Helper::outJson(array('state' => 0 , 'msg' => '请选择公众号!'));
            }

            if (empty($settlement_type)){
                Helper::outJson(array('state' => 0 , 'msg' => '请选择结算类型!'));
            }

            if (empty($start_date) || empty($end_date)){
                Helper::outJson(array('state' => 0 , 'msg' => '请选择投放日期!'));
            }

            if (empty($gaining_followers)){
                Helper::outJson(array('state' => 0 , 'msg' => '请输入计划增预期增加的粉丝!'));
            }

            // 成所需模型实例化
            $official_accounts = Helper::M('OfficialAccounts');
            $Plan = Helper::M('Plan');

            //验证公众号是否存在
            $result_pn_id = $official_accounts->selectOfficialAccounts('pn_id='.$pn_id.' and pn_add_uid='.$uinfo['staff_id']);
            if (!$result_pn_id) {
                Helper::outJson(array('state' => 0 , 'msg' => '您没有绑定该公众号!'));
            }

            // 验证结算类型的合法性
            if (!in_array($settlement_type,array(1,2))) {
                Helper::outJson(array('state' => 0 , 'msg' => '结算类型不合法!'));
            }

            // 验证投放日期的合法性
            if (!($this->checkDateTime($start_date) && $this->checkDateTime($end_date) && strtotime($start_date)<=strtotime($end_date))) {
                Helper::outJson(array('state' => 0 , 'msg' => '投放日期不合法!'));
            }

            // 验证粉丝数的合法性
            if (!is_int($gaining_followers) || $gaining_followers <= 0) {
                Helper::outJson(array('state' => 0 , 'msg' => '增加粉丝不合法!'));
            }

            // var_dump($plan_id);
            // var_dump($Plan->getAuthConfig('plan_dsp_status=3 and plan_id='.$plan_id));

            // 判断是否符合修改要求
            if (!$Plan->getAuthConfig('plan_dsp_status=3 and plan_id='.$plan_id) && !$Plan->getAuthConfig('plan_dsp_status=-1 and plan_id='.$plan_id)) {
                Helper::outJson(array('state' => 0 , 'msg' =>'不符修改计划要求' ));
            }

            // 计算出参考金额
            $order_amount = $this->getReferenceAmount($gaining_followers,$settlement_type);

            $data = [
                'plan_id'=>$plan_id,
                'message'=>'申请修改投放信息',
                'event_type'=>2,
                'change_plan_name'=>$plan_name,
                'change_settlement_type'=>$settlement_type,
                'change_gaining_followers'=>$gaining_followers,
                'change_order_amount'=>$order_amount,
                'change_start_date'=>$start_date,
                'change_end_date'=>$end_date,
            ];

            // 非白名单计划状态
            $new_data = [
                'event_status'=>1
            ];
            $data_insert = array();

            //白名单计划状态
            if ($official_accounts->is_whitelist($pn_id)){
                $new_data = [
                    'event_status'=>2
                ];

                // 申请信息
                $data_insert['agent_events'] = array_merge($data,$new_data);
                // 更新信息
                $data_insert['update_data'] =[
                    'plan_name'=>$plan_name,
                    'pn_id'=>$pn_id,
                    'settlement_type'=>$settlement_type,
                    'gaining_followers'=>$gaining_followers,
                    'order_amount'=>$order_amount,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    'plan_dsp_status'=>2,
                    'plan_boss_status'=>4,
                    'agent_type'=>0,
                    'is_settlement'=>0
                ];

                //白名单直接更新
                $plan_id = $Plan->update($data_insert,$plan_id);
                if ($plan_id) {
                    Helper::outJson(array('state'=>1,'msg'=>"修改计划成功"));
                }else{
                    Helper::outJson(array('state'=>0,'msg'=>"修改计划失败"));
                }

            }else {
                $data_insert = array_merge($data,$new_data);
                // 插入数据库
                $plan_id = $Plan->applyModification($data_insert,$plan_id);

                if ($plan_id) {
                    Helper::outJson(array('state'=>1,'msg'=>"申请修改计划成功"));
                }else{
                    Helper::outJson(array('state'=>0,'msg'=>"申请修改计划失败"));
                }
            }

        }else{
            Helper::outJson(array('state'=>0,'msg'=>"非法请求"));
        }

    }


    /**
     * [getPlanStateMessageAction 得到计划状态信息]
     * @return [type] [description]
     */
    public function getPlanStateMessageAction()
    {
        $plan_id = intval(Helper::clean($this->getRequest()->getParam('plan_id')));
        if (empty($plan_id)) {
            Helper::outJson(array('state' => 0 , 'msg' =>'计划id未指定' ));
        }
        $plan = Helper::M('Plan');
        $data = $plan->getPlanStateMessage($plan_id);
        Helper::outJson(array('state' => 1 , 'data' => $data));

    }


    protected function tmpPayment($tmp_amount,$agent_event_id)
    {
        //调用冻结接口，成功后
        $plan = Helper::M('Plan');
        $id = $plan->tmpPayment($tmp_amount,$agent_event_id);
        if ($id) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * [paymentAction 支付]
     * @return [type] [description]
     */
    public function paymentAction()
    {
        if (isset($_POST) && !empty($_POST)) {

            $plan_id = intval(Helper::clean($this->getRequest()->getPOST('plan_id')));

            if (empty($plan_id)) {
                Helper::outJson(array('state' => 0 , 'msg' =>'计划id未指定' ));
            }

            // 得到支付详情
            $paymentInfo = $this->getPaymentInfoAction($plan_id);
            $plan = Helper::M('Plan');

            // 判断是否是临时支付
            if ($paymentInfo['tmp_amount_state'] == 1) {

                // 判断是否符合支付要求
                if (!$plan->getAuthConfig('plan_dsp_status=3 and plan_id='.$plan_id)) {
                    Helper::outJson(array('state' => 0 , 'msg' =>'不符合支付要求' ));
                }

                $result = $this->tmpPayment($paymentInfo['tmp_amount'],$paymentInfo['event_id']);

                if ($result) {
                    Helper::outJson(array('state' => 1 , 'msg' =>'支付成功' ));
                }else{
                    Helper::outJson(array('state' => 0 , 'msg' =>'支付失败' ));
                }

            }elseif ($paymentInfo['tmp_amount_state'] == 2) {

                // 判断是否符合支付要求
                if (!$plan->getAuthConfig('plan_dsp_status=4 and plan_id='.$plan_id)) {
                    Helper::outJson(array('state' => 0 , 'msg' =>'不符合支付要求' ));
                }

                // 判断支付方式
                if ($plan->getpAymentMethod($plan_id)['payment_method'] == 0 ) {
                    //垫付调用结算接口,成功后
                    $data =[
                        'settlement_amount'=>$paymentInfo['settlement_amount'],
                        'plan_dsp_status'=>5,
                        'plan_boss_status'=>7
                    ];

                    $result = $plan->ayment($data,0,$plan_id);
                }elseif ($plan->getpAymentMethod($plan_id)['payment_method'] == 1 ) {
                    # 直接支付调用冻结接口，成功后
                    $data = [
                        'freezing_amount'=>$paymentInfo['order_amount'],
                        'plan_dsp_status'=>2,
                        'plan_boss_status'=>4
                    ];

                    $result = $plan->ayment($data,1,$plan_id);
                }else{
                    Helper::outJson(array('state' => 0 , 'msg' =>'支付类型错误' ));
                }


                if ($result) {
                    Helper::outJson(array('state' => 1 , 'msg' =>'支付成功' ));
                }else{
                    Helper::outJson(array('state' => 0 , 'msg' =>'支付失败' ));
                }

            }
        }else {
            Helper::outJson(array('state' => 0 , 'msg' =>'非法请求' ));
        }
    }

    /**
     * [getPaymentInfoAction 得到支付计划的详情]
     * @return [type] [description]
     */
    protected function getPaymentInfoAction($plan_id)
    {

        if (empty($plan_id)) {
            Helper::outJson(array('state' => 0 , 'msg' =>'计划id未指定' ));
        }
        $plan = Helper::M('Plan');

        $data = $plan->getPlanInfo($plan_id);

        // 判断代理商类型
        if ($data['agent_type'] == 1) {
            unset($data['agent_id']);
            unset($data['agent_name']);
        }elseif ($data['agent_type'] == 2) {
            unset($data['sc_name']);
            unset($data['agent_id']);
        }

        // 补差价流程
        if ($data['tmp_amount_state'] == 1 && !empty($data['event_id'])) {

            // 通过事件id得到对应的修改信息
            $data_event = $this->getEventInfo($data['event_id']);
            // 修改流程需要显示
            $data['change_gaining_followers'] = $data_event['change_gaining_followers'];
            $data['end_date'] = $data_event['change_end_date'];
            $data['plan_name'] = $data_event['change_plan_name'];

        }elseif ($data['tmp_amount_state'] == 2) {
            unset($data['tmp_amount']);
            unset($data['event_id']);
        }

        // 判断结算类型
        if ($data['settlement_type'] == 1) {
            unset($data['new_add_followers']);
        }elseif ($data['settlement_type'] == 2) {
            unset($data['net_growth_followers']);
        }

        // 垫付
        if ($data['payment_method'] == 0) {

            // 去掉订单金额
            unset($data['order_amount']);
            // 去掉预计增加粉丝
            unset($data['gaining_followers']);

            // 新增粉丝删除净增粉丝
            if ($data['settlement_type'] == 2 ) {

                unset($data['net_growth_followers']);

            // 净增粉丝删除新增粉丝
            }else if($data['settlement_type'] == 1 ){
                unset($data['new_add_followers']);
            }


        // 直接支付 删除新增粉丝，净增粉丝，结算金额
        }elseif ($data['payment_method'] == 1 ) {

            unset($data['new_add_followers']);
            unset($data['net_growth_followers']);
            unset($data['settlement_amount']);

        }

        return $data;
    }



    protected function getEventInfo($event_id)
    {

        $plan = Helper::M('Plan');

        $data = $plan->getEventInfo($event_id);

        return $data;
    }

    /**
     * [getPaymentInfoApiAction 得到支付信息]
     * @return [type] [description]
     */
    public function getPaymentInfoApiAction()
    {
        $plan_id = intval(Helper::clean($this->getRequest()->getParam('plan_id')));
        if (empty($plan_id)) {
            Helper::outJson(array('state' => 0 , 'msg' =>'计划id未指定' ));
        }
        $rel = $this->getPaymentInfoAction($plan_id);

        if ($rel['payment_method'] == 0) {

            $rel['amount'] = $rel['settlement_amount'];
            unset($rel['settlement_amount']);

            // 新增粉丝删除净增粉丝
            if ($rel['settlement_type'] == 2 ) {

                $rel['followers'] = $rel['new_add_followers'];
                unset($rel['new_add_followers']);

            // 净增粉丝删除新增粉丝
            }else if($rel['settlement_type'] == 1 ){
                $rel['followers'] = $rel['net_growth_followers'];
                unset($rel['net_growth_followers']);
            }

        // 直接支付
        }elseif ($rel['payment_method'] == 1 ) {

            $rel['amount'] = $rel['order_amount'];
            unset($rel['order_amount']);
            $rel['followers'] = $rel['gaining_followers'];
            unset($rel['gaining_followers']);
        }
        unset($rel['payment_method']);
        unset($rel['event_id']);

        Helper::outJson(array('state' => 1 , 'data' => $rel));
    }

    /**
     * [stopPlanAction 停止计划]
     * @return [type] [description]
     */
    public function stopPlanAction()
    {
        $plan_id = intval(Helper::clean($this->getRequest()->getPost('plan_id')));
        if (empty($plan_id)) {
            Helper::outJson(array('state' => 'error' , 'msg' =>'计划id未指定' ));
        }
        $plan = Helper::M('Plan');
        $official_accounts = Helper::M('OfficialAccounts');
        // 得到计划信息
        $info = $plan->getAuthConfig('plan_id='.$plan_id);
        // 检查计划合法性
        $check = $this->checkAccountPlan($plan_id);

        if (!$info || !$check) {
            Helper::outJson(array('state' => 0 , 'msg' =>'计划信息不存在' ));
        }

        // 只有排队中和已投放可以停止投放
        if (intval($info['plan_dsp_status']) != 2 && intval($info['plan_dsp_status']) != 3) {

            Helper::outJson(array('state' => 0 , 'msg' =>'不符合停止投放要求' ));
        }

        // 白名单直接停止
        if ($official_accounts->is_whitelist($info['pn_id'])) {

        }else{
            // 非白名单
            $rel = $plan->stopPlan($plan_id);
            if ($rel) {
                Helper::outJson(array('state' => 1 , 'msg' =>'申请停止计划成功' ));
            }else{
                Helper::outJson(array('state' => 0 , 'msg' =>'申请停止计划失败' ));
            }
        }
    }

    /**
     * [cancelPlan 取消投放]
     * @return [type] [description]
     */
    public function cancelPlanAction()
    {
        $plan_id = intval(Helper::clean($this->getRequest()->getParam('plan_id')));
        if (empty($plan_id)) {
            Helper::outJson(array('state' => 0 , 'msg' =>'计划id未指定' ));
        }
        $plan = Helper::M('Plan');
        // 得到计划信息
        $info = $plan->getAuthConfig('plan_id='.$plan_id);

        // 检查计划合法性
        $check = $this->checkAccountPlan($plan_id);

        if (!$info || !$check) {
            Helper::outJson(array('state' => 0 , 'msg' =>'计划信息不存在' ));
        }

        // 只有审核失败可以取消投放
        if ($info['plan_dsp_status'] != -1 ) {
            Helper::outJson(array('state' => 0 , 'msg' =>'不符合取消投放要求' ));
        }
        // 取消投放
        $rel = $plan->cancelPlan($plan_id);
        if ($rel) {
            Helper::outJson(array('state' => 1 , 'msg' =>'取消投放成功' ));
        }else{
            Helper::outJson(array('state' => 0 , 'msg' =>'取消投放失败' ));
        }

    }

    /**
     * [checkAccountPlan 检查计划的合法性]
     * @param  [type] $plan_id [计划id]
     * @return [bool]          [description]
     */
    protected function checkAccountPlan($plan_id)
    {

        $plan = Helper::M('Plan');
        $rel = $plan->checkAccountPlan($plan_id);
        if ($rel) {
            return true;
        }else{
            return false;
        }

    }






}
