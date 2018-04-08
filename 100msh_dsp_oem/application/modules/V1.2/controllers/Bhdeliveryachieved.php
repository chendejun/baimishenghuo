<?php

/**
 *
 */
class BhDeliveryAchievedController extends BasicController
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


    public function advertisedListAction()
    {
        $bh_delivery_id = Helper::clean($this->getRequest()->getParam('bh_delivery_id'));
        $start_date = Helper::clean($this->getRequest()->getParam('start_date',date('Y-m-d',time())));
        $end_date = Helper::clean($this->getRequest()->getParam('end_date',date('Y-m-d',time())));
        $this->checkParam('广告id',$bh_delivery_id);
        $this->checkParam('开始时间',$start_date);
        $this->checkParam('结束时间',$end_date);
        $param = [
            'bh_delivery_id'=>$bh_delivery_id,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
        ];
        $result = BmDspApi::request('BhDeliveryAchieved/advertisedList',$param);
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('state'=>self::SUCCESS_STATE,'data'=>$result['data']));
        }
        Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>'失败'));
    }


    public function getAdvertAreaInfoAction()
    {

        $bh_delivery_id = Helper::clean($this->getRequest()->getParam('bh_delivery_id'));
        $type = Helper::clean($this->getRequest()->getParam('type'));
        $start_date = Helper::clean($this->getRequest()->getParam('start_date',date('Y-m-d',time())));
        $end_date = Helper::clean($this->getRequest()->getParam('end_date',date('Y-m-d',time())));
        $this->checkParam('广告id',$bh_delivery_id);
        $this->checkParam('维度',$type);
        $this->checkParam('开始时间',$start_date);
        $this->checkParam('结束时间',$end_date);

        $param = [
            'bh_delivery_id'=>$bh_delivery_id,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'type'=>$type
        ];
        $result = BmDspApi::request('BhDeliveryAchieved/getAdvertAreaInfo',$param);
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('state'=>self::SUCCESS_STATE,'data'=>$result['data']));
        }
        Helper::outJson(array('state'=>self::FAIL_STATE,'msg'=>'失败'));

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
