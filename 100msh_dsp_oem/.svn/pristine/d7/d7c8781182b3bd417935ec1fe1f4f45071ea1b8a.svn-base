<?php
class Agent extends Model
{
    function getMonthAmt($date_start,$date_end){
        $res= $this->db->account->query("SELECT count(1) as recharge_num,sum(real_pay_amount) as deliver_amount,sum(amount) as transfer_amount FROM dsp_boss_recharge WHERE status=1 AND transfer_time >= '{$date_start}' AND transfer_time <= '{$date_end} 23:59:59'");
        return $res[0];
    }
    function getBossId(){
        $res=  $this->db->dsp->table("dsp_company")->where('comp_type=3')->find();
        return $res['comp_id'];
    }
    function upAddProfit($data){
        return $this->db->account->execute("REPLACE INTO dsp_profit_month (`recharge_num`,`deliver_amount`,`transfer_amount`,`top_portrait`,`app_portrait`,`stat_month`) VALUES ({$data['recharge_num']},{$data['deliver_amount']},{$data['transfer_amount']},{$data['top_portrait']},{$data['app_portrait']},{$data['stat_month']})" );
    }
}
