<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/4
 * Time: 12:00
 */
class ReportController extends BasicController{
    /**
     * 效果列表
     */
    public function indexAction(){}

    /**
     * 效果详细
     */
    public function infoAction(){}


    /**
     * 统计接口
     */
    public function countAction(){
        Yaf_Dispatcher::getInstance()->disableView();
        $deliveryId = Helper::clean($this->getRequest()->getParam('deliveryid','0'));
        $startTime = Helper::clean($this->getRequest()->getParam('starttime' ,''));
        $endTime = Helper::clean($this->getRequest()->getParam('endtime' ,''));
        if(!empty($startTime)&&!empty($endTime)){
            //判断日期差有没有大于1个月
           $maxStartTime =  strtotime("$endTime -1 month");
           $realStarTime = strtotime($startTime);
           if($realStarTime<$maxStartTime){
               Helper::outJson(array('state'=>0,'msg'=>'开始和结束时间不能大于1个月'));
               exit;
           }

        } else {
            //默认时间开始日期为投放日期
            $deliModel = Helper::M("Delivery");
            $deliinfo  = $deliModel->getDeliveryInfo($deliveryId);
            $startTime = $deliinfo['delivery_start_date'];
            $now_day = date("Y-m-d",time());
            if($startTime>=$now_day){
                $endTime = date("Y-m-d",time()-3600*24);
                $startTime = date("Y-m-d",strtotime("$endTime -1 month"));
            } else {
                if($deliinfo['delivery_end_date']>=$now_day){
                    $endTime = date("Y-m-d",time()-3600*24);
                } else {
                    $endTime = $deliinfo['delivery_end_date'];
                }
            }


        }
        $reportModel = Helper::M("Report");
        $count = $reportModel->count($deliveryId,$startTime,$endTime);
        $expendCount = $reportModel->finceexpend($deliveryId,$startTime,$endTime);
        $countlist = array();
        $daySum = (strtotime($endTime)-strtotime($startTime))/3600/24;
        for($i=0;$i<=$daySum;$i++){
            $countlist[$i]['stat_day'] = date("Y-m-d",strtotime("$endTime -$i   day"));
            $countlist[$i]['cpm_num'] = 0;
            $countlist[$i]['cpc_num'] = 0;
            $countlist[$i]['con_rate'] = 0;
            $countlist[$i]['consume_amount'] = 0;
            foreach($count as $k=>$v){
             if($v['stat_day'] == $countlist[$i]['stat_day'] ){
                 $countlist[$i]['cpm_num'] = $v['cpm_num'];
                 $countlist[$i]['cpc_num'] = $v['cpc_num'];
                 $countlist[$i]['con_rate'] = $v['con_rate'];
             }
            }
            foreach($expendCount as $key=>$val){
                if($val['count_date'] == $countlist[$i]['stat_day'] ){
                    $countlist[$i]['consume_amount'] =Helper::transAmount($val['consume_amount'],1);
                }
            }

        }


        if($countlist){
            Helper::outJson(array('state'=>1,'msg'=>'','data'=>$countlist,));
        } else {
            Helper::outJson(array('state'=>2,'msg'=>'暂无统计数据'));
        }

    }







}