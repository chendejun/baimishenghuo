<?php
/**
 * restful 核心认证类
 * @author :wzw
 **/
class Headline extends  Model{




    /*
     * 添加头条广告
     */
     public function  add($data){
         $jr_id= $this->db->dsp->table( 'dsp_jr_delivery')->data ( $data )->insert ();
         return $jr_id;
     }
     /*
      * 获取详情
      */
     public function getInfo($jr_id){
         $data = $this->db->dsp->query("SELECT  dsp_jr_delivery.*,dsp_jr_delivery_audit.audit_remark    FROM `dsp_jr_delivery`  LEFT JOIN  dsp_jr_delivery_audit ON dsp_jr_delivery.jr_delivery_id = dsp_jr_delivery_audit.delivery_id   WHERE dsp_jr_delivery.jr_delivery_id = ".$jr_id);
         return $data;
     }
     /*
      * 列表
      */
     public  function getList($comp_pid,$start = 0 , $limit = ''){
         $limit_str = '';
         if (!empty($limit)) {
             $limit_str = ' limit '.$start.','.$limit;
         }
         $sql = "SELECT dsp_jr_delivery.*,dsp_jr_delivery_audit.audit_remark  FROM dsp_jr_delivery  LEFT  JOIN  dsp_jr_delivery_audit ON dsp_jr_delivery.jr_delivery_id  = dsp_jr_delivery_audit.delivery_id WHERE dsp_jr_delivery.comp_pid = $comp_pid  order by add_time desc ".$limit_str;
         $data = $this->db->dsp->query($sql);
         return $data;
     }
     /*
      *
      */
     public function getCountList($comp_pid){
         $rel = $this->db->dsp->table('dsp_jr_delivery')->where(array('comp_pid'=>$comp_pid))->count();
         return $rel;
     }
    /*
     *
     */
    public function getAduit($jr_delivery_id){
        $data = $this->db->dsp->query("SELECT  *    FROM `dsp_jr_delivery_audit`  WHERE delivery_id = ".$jr_delivery_id);
        return $data;
    }

}
