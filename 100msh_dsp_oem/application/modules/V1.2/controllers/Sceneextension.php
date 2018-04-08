<?php

/**
 *
 */
class SceneextensionController extends BasicController
{

    /**
     * [SUCCESS_STATE 请求成功状态]
     * @var integer
     */
    const SUCCESS_STATE = 1;

    /**
     * [FAIL_STATE 请求失败状态]
     * @var integer
     */
    const FAIL_STATE = 0;

    /**
     * [getAllServerSceneResource 获取指定所有服务商场景资源 002]
     * @return [array] [description]
     */
    public function getAllServerSceneResourceAction()
    {

        $comp_id = Helper::clean($this->getRequest()->getParam('comp_id'));
        $is_self = Helper::clean($this->getRequest()->getParam('is_self'));
        if (!empty($is_self) && $is_self == 1 ) {
            $uinfo = $_SESSION['uinfo'];
            if (empty($comp_id)) {
                $comp_id = $uinfo['comp_id'];
            }else{
                $comp_id = $comp_id.','.$uinfo['comp_id'];
            }


        }
        $this->checkParam('服务商id',$comp_id);
        $result = BmDspApi::request('Scene/getAllServerSceneResource', array('comp_id' => $comp_id));
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('state'=>self::SUCCESS_STATE,'data'=>$result['data']));
        }
        Helper::outJson(array('state'=>self::FAIL_STATE,'data'=>''));

    }


    public function getServerSmsInfoAction()
    {
        $uinfo = $_SESSION['uinfo'];
        $comp_id_csv = Helper::clean($this->getRequest()->getParam('comp_id_csv',$uinfo['comp_id']));

        $this->checkParam('服务商id',$comp_id_csv);

        $result = BmDspApi::request('Scene/getServerSmsInfo', array('comp_id_csv' => $comp_id_csv));
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('state'=>self::SUCCESS_STATE,'data'=>$result['data']));
        }
        Helper::outJson(array('state'=>self::FAIL_STATE,'data'=>''));

    }


    /**
     * [getTransferResourceRecord 获取转移资源记录列表 003]
     * @param  int $comp_id [description]
     * @param  int $transfer_type [转移类型 1支出，2被充值]
     * @param  int $customer_type [消耗类型(1 转出代理商 2自由广告主开通 )]
     * @param  int $secen_type [画像类型(2 高级版 3 APP版)]
     * @param  int $add_start_time [开始时间]
     * @param  int $add_end_time [结束时间]
     *
     * @return [type] [description]
     */
    public function getTransferResourceRecordAction()
    {

        $comp_id = Helper::clean($this->getRequest()->getParam('comp_id'));
        $transfer_type = Helper::clean($this->getRequest()->getParam('transfer_type'));
        $record_status = Helper::clean($this->getRequest()->getParam('record_status'));
        $customer_type = Helper::clean($this->getRequest()->getParam('customer_type'));
        $secen_type = Helper::clean($this->getRequest()->getParam('secen_type'));
        $server_name = Helper::clean($this->getRequest()->getParam('server_name'));
        $date = Helper::clean($this->getRequest()->getParam('date'));
        $page = Helper::clean($this->getRequest()->getParam('page',1));
        $pagecount = Helper::clean($this->getRequest()->getParam('pagecount',10));

        $this->checkParam('转移类型',$transfer_type,array(1,2));
        if (empty($record_status)) {
            $record_status = 1;
        }
        $add_start_time = '';
        $add_end_time = '';
        if (empty($comp_id)) {
            $uinfo = $_SESSION['uinfo'];
            $comp_id = $uinfo['comp_id'];
        }
        $this->checkParam('服务商id',$comp_id);
        if (!empty($date)) {
            $time = strtotime($date.'-01');
            $day = date('t', $time);
            $add_start_time = $date.'-01';
            $add_end_time = $date.'-'.$day;
        }
        $param = [
            'comp_id'=>$comp_id,
            'transfer_type'=>$transfer_type,
            'record_status'=>$record_status,
            'customer_type'=>$customer_type,
            'secen_type'=>$secen_type,
            'server_name'=>$server_name,
            'add_start_time'=>$add_start_time,
            'add_end_time'=>$add_end_time,
            'page'=>$page,
            'pagecount'=>$pagecount
        ];

        $result = BmDspApi::request('Scene/getTransferResourceRecord', $param);
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data']['data'])) {
            $pages = ceil($result['data']['count']/$pagecount);
            Helper::outJson(array('state'=>self::SUCCESS_STATE,'data'=>array('list'=>$result['data']['data'], 'pages'=>$pages,'page'=>$result['data']['page'],'total'=>$result['data']['total_num'])));
        }
        Helper::outJson(array('state'=>self::FAIL_STATE,'data'=>''));
    }


    /**
     * [getServerSceneUseInfo ，获取服务商场景支出信息（数量） 005]
     * @return [type] [description]
     */
    public function getServerSceneUseInfoAction()
    {
        $comp_id = Helper::clean($this->getRequest()->getParam('comp_id'));
        if (empty($comp_id)) {
            $uinfo = $_SESSION['uinfo'];
            $comp_id = $uinfo['comp_id'];
        }
        $this->checkParam('服务商id',$comp_id);
        $param = [
            'comp_id'=>$comp_id
        ];
        $result = BmDspApi::request('Scene/getServerSceneUseInfo', $param);
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data']['data'])) {
            Helper::outJson(array('state'=>self::SUCCESS_STATE,'data'=>$result['data']['data']));
        }
        Helper::outJson(array('state'=>self::FAIL_STATE,'data'=>''));

    }

    /**
     * [transferServerSceneResource 转移服务商场景资源 006]
     * @param  int $from_comp_id [源服务商id]
     * @param  int $to_comp_id [目标服务商id]
     * @param  int $resource_type [资源类型 2高级版 3app分析版]
     * @param  int $transfer_number [转移资源数量]
     * @param  int $staff_id [操作人id]
     * @return [type] [description]
     */
    public function transferServerSceneResourceAction()
    {

        $uinfo = $_SESSION['uinfo'];
        $staff_id = $uinfo['staff_id'];
        if (isset($_POST) && !empty($_POST)) {
            $record_id = Helper::clean($this->getRequest()->getPost('record_id'));
            $from_comp_id = Helper::clean($this->getRequest()->getPost('from_comp_id'));
            $to_comp_id = Helper::clean($this->getRequest()->getPost('to_comp_id'));
            $resource_type = Helper::clean($this->getRequest()->getPost('resource_type'));
            $transfer_number = Helper::clean($this->getRequest()->getPost('transfer_number'));
            $code = Helper::clean($this->getRequest()->getPost('code'));
            $param = array();
            if (empty($record_id)) {
                $this->checkCode($code);
                if (empty($from_comp_id)) {
                    $uinfo = $_SESSION['uinfo'];
                    $from_comp_id = $uinfo['comp_id'];
                }
                $this->checkParam('源服务商id',$from_comp_id);
                $this->checkParam('目标服务商id',$to_comp_id);
                $this->checkParam('资源类型',$resource_type);
                $this->checkParam('转移资源数量',$transfer_number);
                $param = [
                    'from_comp_id'=>$from_comp_id,
                    'to_comp_id'=>$to_comp_id,
                    'resource_type'=>$resource_type,
                    'transfer_number'=>$transfer_number,
                    'staff_id'=>$staff_id
                ];
            }else {
                $param = [
                    'record_id'=>$record_id
                ];
            }
            //var_dump($param);
            $result = BmDspApi::request('Scene/transferServerSceneResource', $param,'POST');
            if (!empty($result) && $result['api_code'] == 0 && isset($result['data']['state'])) {
                if ($result['data']['state'] == -1) {
                    Helper::outJson(array('state'=>$result['data']['state'],'msg'=>'余额不足'));
                }elseif ($result['data']['state'] == 0) {
                    Helper::outJson(array('state'=>$result['data']['state'],'msg'=>'操作失败，请重试'));
                }
                Helper::outJson(array('state'=>$result['data']['state']));
            }
            Helper::outJson(array('state'=>self::FAIL_STATE));
        }
        Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>'非法请求'));

    }

    /**
     * [sendShortMessageAction 发送短信]
     * @return [type] [description]
     */
    public function sendShortMessageAction()
    {

        $num = Helper::clean($this->getRequest()->getParam('num'));
        $this->checkParam('请设置转移资源数量',$num);
        $uinfo = $_SESSION['uinfo'];
        $code_key = $uinfo['staff_id'];
        $code = $this->getRandomCode();
        $mobile_phone = $this->getMobilePhone();
        $content = '付款验证码：'.$code.'，您确定向该服务商转移数量为'.$num.'的人群画像资源，请在3分钟内填写验证码完成验证。';
        $_SESSION['code_key'.$code_key] = $code;
        $result = BmDspApi::request('sms/send' , array('content' => $content ,'mobile_phone'=>$mobile_phone)); //发送短信验证码
        if(empty($result) || isset($result['error'])){
         Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>empty($result)?'短信发送异常，请稍后再试！':$result['error']));
        }else{
         Helper::outJson(array('state'=>self::SUCCESS_STATE,'msg'=> $result['msg']));
        }
    }


    /**
     * [getMobileTailPhoneAction 得到手机号码尾号]
     * @return [type] [description]
     */
    public function getMobileTailPhoneAction()
    {

        $phone = $this->getMobilePhone();
        Helper::outJson(array('state'=>self::SUCCESS_STATE,'phone'=>substr($phone,-4,4)));

    }

    /**
     * [getRandomCode 得到随机码]
     * @return [type] [description]
     */
    protected function getRandomCode()
    {
         return rand(1000,9999);
    }

    /**
     * [getMobilePhone 得到手机号码]
     * @return [type] [description]
     */
    protected function getMobilePhone()
    {
        $uinfo = $_SESSION['uinfo'];
        $scenes = Helper::M('Scenes');
        $phone = $scenes->getMobilePhone($uinfo['comp_id']);
        return $phone;
    }


    /**
     * [checkCode 验证验证码输入是否正确]
     * @param  [type] $code [输入的验证码]
     * @return [type]       [description]
     */
    protected function checkCode($code)
    {
        $uinfo = $_SESSION['uinfo'];
        $code_key = $uinfo['staff_id'];
        if (empty($code)) {
            Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>"请输入验证码"));
        }
        if (empty($_SESSION['code_key'.$code_key]) || !isset($_SESSION['code_key'.$code_key])) {
            Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>"短信session失效！"));
        }
        if ($_SESSION['code_key'.$code_key] != $code) {
            Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>"验证码输入错误！"));
        }
        $_SESSION['code_key'.$code_key] = '';
    }

    protected function checkParam($param_column,$param_value,$param_Range = array())
    {
        if (empty($param_value)) {
            Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>'请输入：'.$param_column));
        }
        if (count($param_Range)>0) {
            if (!in_array($param_value,$param_Range)) {
                Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>'参数'.$param_column.'不合法'));
            }
        }
    }
}
