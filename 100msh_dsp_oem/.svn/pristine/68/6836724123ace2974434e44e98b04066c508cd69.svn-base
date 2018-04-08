<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/22
 * Time: 11:50
 */
Class Material extends Model{
  /**
   * 查询所有的广告组
   */
  public function adgroupList(){
     $groupList =  $this->db->dsp->query("SELECT adgroup_id,adgroup_name,adgroup_show_url FROM dsp_adgroup");
     if(empty($groupList)){
         return false;
     } else {return $groupList;};

  }

  /**
   * 查询当前默认的广告平台对应的所有广告位
   */
  /*
  public function adsizeList(){
      $platform_id = 1;//默认为百米平台;
      $sql ="
SELECT az.ad_size_id,az.ad_size_name,az.ad_size_width,az.ad_size_height ,az.adgroup_id FROM dsp_ad_position AS ppr
JOIN dsp_ad_size AS az ON az.ad_size_id = ppr.ad_size_id
JOIN dsp_adgroup AS adg ON adg.adgroup_id = az.adgroup_id WHERE ppr.platform_id=$platform_id";
      $adsizeList =  $this->db->dsp->query($sql);
      if(empty($adsizeList)){
          return  false;
      } else {
          //按广告组分类
          $newAdSizeList = array();
          foreach($adsizeList as $k=>$v){
              $key = $v['adgroup_id'];
              $newAdSizeList[$key][] = $v;
          };
          return $newAdSizeList;
      }

  }*/


    /**
     * 查询所有的广告组以及其对应的素材
     *
     */
    public function getAdgroupList(){
        $platform_id = 1;//默认为百米平台;
        $groupList =  $this->db->dsp->query("SELECT adgroup_id,adgroup_name,adgroup_show_url FROM dsp_adgroup");
       foreach($groupList as $key=>$value){
            $group_id = $value['adgroup_id'];
            $sql = "SELECT az.ad_size_id,az.ad_size_name,az.ad_size_width,az.ad_size_height ,az.adgroup_id FROM dsp_ad_position AS ppr JOIN dsp_ad_size AS az ON az.ad_size_id = ppr.ad_size_id JOIN dsp_adgroup AS adg ON adg.adgroup_id = az.adgroup_id WHERE ppr.platform_id=$platform_id and az.adgroup_id=$group_id";
            $res = $this->db->dsp->query($sql);
            $groupList[$key]['ad_size_info'] = $res;


        }
        return $groupList;
    }
    /**
     * 根据规格id，查询规格
     */
    public function getSizeInfo($sizeid){
        $res = $this->db->dsp->query("SELECT `ad_size_id` AS `id`,`ad_size_width` AS `width`,ad_size_height AS `height` FROM `dsp_ad_size` WHERE ad_size_id=$sizeid");
        return $res[0];
    }



    /**
     * 添加素材
     * @param $data
     *
     */
  public function addMaterial($data){
      $materialId = $this->db->dsp->table('dsp_material_info')->data($data)->insert();
      if(!empty($materialId)){
          return $materialId;
      } else {
          return false;
      }

  }
  /**
   * 添加素材规格
   */
  public function addMaterialSzie($data){
      $materialSizeId = $this->db->dsp->table('dsp_material_size_list')->data($data)->insert();
      if(!empty($materialSizeId)){
          return true;
      } else {
          return false;
      }
  }

    /**
     * 查询素材总数
     */
    public function getMaterialCount($where){
        $result = $this->db->dsp->query("SELECT count(1) as num FROM `dsp_material_info` AS mat WHERE {$where}");
        return empty($result)?0:$result[0]['num'];
    }

    /**
     * 素材列表
     * @param $where
     * @param int $start
     * @param int $limit
     * @return mixed
     */
    public function getMaterialList($where , $start = 0 , $limit=15){
        $result = $this->db->dsp->query("SELECT material_id,material_name,material_url,staff_name as addstaff,adgroup_name,FROM_UNIXTIME(mat.add_time) as add_time  FROM `dsp_material_info` as mat  
            JOIN dsp_adgroup AS adg ON adg.adgroup_id = mat.adgroup_id
            LEFT JOIN dsp_comp_staff AS comp ON comp.staff_id = mat.add_staff_id
            WHERE $where ORDER BY `add_time` DESC LIMIT $start,$limit ");
        foreach( $result as $key=>$value){
            $material_id = $value['material_id'];
            $materinfo = $this->db->dsp->query("SELECT img_url FROM dsp_material_size_list WHERE material_id=$material_id LIMIT 0,1");
            $result[$key]['img_url'] = $materinfo[0]['img_url'];
        }
        return $result;
    }

    /**
     * 素材详细
     * @param $id
     * @return mixed
     */
    public function getMaterialInfo($id){
        $result = $this->db->dsp->query("SELECT material_id,material_name,material_url,add_staff_id,mat.adgroup_id,adgroup_name,mat.add_time FROM `dsp_material_info` AS mat 
        JOIN dsp_adgroup AS adg ON adg.adgroup_id = mat.adgroup_id WHERE material_id = $id");
        return $result[0];
    }
    public function getMaterialSizeInfo($id){
        $result =  $this->db->dsp->query("SELECT ms_id,img_url,ad_size_name,msl.ad_size_id,adgroup_id FROM dsp_material_size_list as msl
        JOIN dsp_ad_size as ads ON ads.ad_size_id = msl.ad_size_id
         WHERE material_id=$id");
        return $result;
    }
    /**
     * 修改素材表
     */
    public function updateMaterial($data,$id){
        $where['material_id'] = $id;
        $result = $this->db->dsp->table('dsp_material_info')->data($data)->where($where)->update();
        return $result;
    }
    /**
     * 删除素材表
     */
    public function deleteMaterial($id){
        $result = $this->db->dsp->execute("DELETE FROM `dsp_material_info` WHERE `material_id`=$id");
        return $result;
    }
    /**
     * 删除素材规格对应表
     */
    public function deleteMaterialSize($id){

        $result = $this->db->dsp->execute("DELETE FROM `dsp_material_size_list` WHERE `material_id`=$id");
        return $result;
    }
    /**
     * 更新素材对应表
     */
    public function updateMaterialSize($msid,$url){
        $result = $this->db->dsp->execute("UPDATE dsp_material_size_list SET img_url='$url' WHERE ms_id=$msid");
        return $result;
    }

    /**
     * 根据素材规格id和平台id查询对应的广告位
     */
    public function getAdPostId($adsizeId){

    }

    /*
     *上传文件数据保存
     */
    public function addFile($data){
        $market_file_id = $this->db->sms->table('dsp_market_file')->data($data)->insert();
        return $market_file_id;
    }

}