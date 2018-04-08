<?php

Class BhCity extends  Model{

	/**
     * 查询全网广省市列表
     */
    public function getBhCity($pid=0,$level){ 
        if($level==2){
        	if($pid=='137101101100100' || $pid=='137101102100100' || $pid=='137103101100100' || $pid=='137105101100100' || $pid=='137107102100100' || $pid=='137107103100100' || $pid=='137107101100100'){
        		$where="`level`=1 AND bh_id=$pid ORDER BY bh_id desc";
        	}else{
        		$where="`level`=$level AND `pId` = $pid ORDER BY bh_id desc";
        	}
            
        }else{
            $where="`level`=$level ORDER BY bh_id asc";
        }
        $list =  $this->db->bh->query("SElECT * FROM `bh_city` WHERE $where " );
        return $list;
    }
	/**
     * 查询全网广省市列表
     */
    public function getBhCityArea($type=1,$cityId){ 
        if($type==1){
            $arealist =  $this->db->bh->query("SElECT * FROM `bh_city_area` WHERE `type`=0 " );
        }else{
            $where="`cityId`=$cityId and type in(1,2)";
            $list =  $this->db->bh->query("SElECT * FROM `bh_city_area` WHERE $where" );
            if(!empty($list)){
            	$arealist=array();
            	foreach ($list as $key=>$val){
            		if($val['type']==1){
            			$arealist[$val['bh_area_id']]=$val;
            		}
            	}
            	foreach ($arealist as $key1=>$val1){
	            	foreach ($list as $key=>$val){
		            	if($key1==$val['pId']){
		            		$arealist[$key1]['area_data'][]=$val;
		            	}
		            }
            	}
            }
        }
        return $arealist;
    }
}