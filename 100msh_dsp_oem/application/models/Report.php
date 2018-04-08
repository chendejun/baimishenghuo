<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/4
 * Time: 13:43
 */
class Report extends Model{
  /*
   * 统计（浏览量，点击量，点击率）
   */
    public function count($id,$starttime,$endtime){
        $where = "`delivery_id`=$id and `stat_day`>='$starttime' and `stat_day`<='$endtime'";

        $res = $this->db->stat->query("SELECT `stat_day`,`cpm_num`,`cpc_num`,`con_rate` FROM `dsp_ad_day_stat` WHERE {$where}");
        return $res;
    }
    /**
     * 统计扣费信息
     */
    public function finceexpend($id,$starttime,$endtime){
        $where = "`delivery_id`=$id and `count_date`>='$starttime' and `count_date`<='$endtime'";
        //取出起止时间的年份
        $startYear = substr($starttime,0,4);
        $endYear = substr($endtime,0,4);
        //不跨年
        if($startYear==$endYear){
           $res = $this->db->account->query("SELECT `count_date`,`consume_amount`,`sy_amount` from dsp_stat_consume_{$startYear} WHERE  {$where}");
           return $res;
        } else {
            $resOne = $this->db->account->query("SELECT `count_date`,`consume_amount`,`sy_amount` from dsp_stat_consume_{$startYear} WHERE  {$where}");
            $resTwo = $this->db->account->query("SELECT `count_date`,`consume_amount`,`sy_amount` from dsp_stat_consume_{$endYear} WHERE  {$where}");
           $res =  array_merge($resOne,$resTwo);
           return $res;
        }
    }

}