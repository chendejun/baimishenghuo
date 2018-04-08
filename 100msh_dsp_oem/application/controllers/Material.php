<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/22
 * Time: 18:02
 */

class MaterialController extends BasicController {
    /**
     * 素材列表
     */
    public function indexAction(){
        $page = Helper::clean($this->getRequest()->getParam('page' , 0));
        $materialName = Helper::clean($this->getRequest()->getParam('name' , ''));
        $groupId = Helper::clean($this->getRequest()->getParam('groupid' , ''));
        $limit = Helper::clean($this->getRequest()->getParam('limit' , 15));
        $start = $page>0?($page-1)*$limit:0;
        $where = 'mat.`source_type`=0 AND mat.`comp_pid`=' . $this->uinfo['comp_id'];
        if(!empty($groupId)) $where .=  " AND  mat.adgroup_id =". $groupId;
        if(!empty($materialName)) $where .=  " AND material_name like \"%{$materialName}%\"";
        $materialModel = Helper::M('Material');
        $list = $materialModel->getMaterialList($where , $start , $limit);
        $count = $materialModel->getMaterialCount($where);
        $pages = ceil($count/$limit);
        if($page!=0){
            Helper::outJson(array('state'=>1,'msg'=>'','data'=>array('pages' => $pages , 'list' => $list)));
            exit;
        }

    }
    /**
     * 添加素材
     */
     public function  addAction(){
         $this->config = Yaf_Registry::get('config');
         $ACCESSORY_FOLDER = $this->config['config']['ACCESSORY_FOLDER'];
         $adgroupModel = Helper::M("Material");
         if(isset($_POST)&&!empty($_POST)) {
             //添加素材
             $this->db    = Yaf_Registry::get('db');
             $this->db->dsp->startTrans();
             $materialName = Helper::clean($this->getRequest()->getPost('material_name'));
             $materialUrl = Helper::clean($this->getRequest()->getPost('material_url'));
             $adgroupId = Helper::clean($this->getRequest()->getPost('adgroup_id'));
             $adsizeId = Helper::clean($this->getRequest()->getPost('adsize_id'));
             $fileInfo = Helper::clean($this->getRequest()->getPost('fileinfo'));
             $adsizeId = explode(",", $adsizeId);
             $file_path = explode(",", $fileInfo);
             if (count($adsizeId) != count($file_path)) {
                 Helper::outJson(array('state' => 0, 'msg' => '图片和规格数量不匹配'));
             }
             if (empty($materialName) || empty($materialUrl)) {
                 Helper::outJson(array('state' => 0, 'msg' => '素材名称和跳转url不能为空'));
             }
             if (intval($adgroupId) == 0) {
                 Helper::outJson(array('state' => 0, 'msg' => '请选择素材类型'));
             }
             //添加素材库
             $data['material_name'] = $materialName;
             $data['material_url'] = $materialUrl;
             $data['adgroup_id'] = $adgroupId;
             $data['comp_id'] = 0;//广告主ID
             $data['comp_pid'] = $this->uinfo['comp_id'];//服务商ID
             $data['add_staff_id'] = $this->uinfo['staff_id'];//用户ID
             $data['add_time'] = time();
             $materialId = $adgroupModel->addMaterial($data);
             if (!empty($materialId)) {
                 //添加素材规格
                 foreach ($adsizeId as $key => $value) {
                     $materialSizeData['img_url'] = $file_path[$key];
                     $materialSizeData['material_id'] = $materialId;
                     $materialSizeData['ad_size_id'] = $adsizeId[$key];
                     $res = $adgroupModel->addMaterialSzie($materialSizeData);
                     if (empty($res)) {
                         Helper::outJson(array('state' => 2, 'msg' => '添加失败'));
                         $this->db->dsp->rollback();//100msh_ad广告数据库事务回滚
                         exit;
                     }
                 }
                 //添加操作日志
                 $this->db->dsp->commit();//100msh_ad广告数据库事务提交
                 $log_content[] = "素材管理 :" . "添加素材";
                 $log_content_str = implode(" , ", $log_content);
                 $log_obj = Helper::M('Log');
                 $log_obj->create_log('素材添加', $log_content_str, 'LC003', "LOT001");
                 Helper::outJson(array('state' => 1, 'msg' => '添加成功'));
                 exit;
             } else {
                 Helper::outJson(array('state' => 1, 'msg' => '添加失败'));
             }

         }


     }
     /**
      * 素材详情
      */
     public function infoAction(){
         $materialId = Helper::clean($this->getRequest()->getParam('id' , ''));
         $materialModel = Helper::M('Material');
         $materialInfo = $materialModel->getMaterialInfo($materialId);
         $materialSizeInfo = $materialModel->getMaterialSizeInfo($materialId);
         $this->getView()->assign('materialInfo',$materialInfo);
         $this->getView()->assign('materialSizeInfo', $materialSizeInfo);
         Helper::outJson(array('state'=>1,'msg'=>'','data'=>array('materialInfo' => $materialInfo , 'materialSizeInfo' => $materialSizeInfo)));
     }
     /**
      * 修改素材
      */
     public function editAction(){
         if(isset($_POST)&&!empty($_POST)) {
             $this->config = Yaf_Registry::get('config');
             $ACCESSORY_FOLDER = $this->config['config']['ACCESSORY_FOLDER'];
             $ACCESSORY_URL = $this->config['config']['ACCESSORY_URL'];
             $this->db    = Yaf_Registry::get('db');
             $this->db->dsp->startTrans();
             $materialModel = $materialModel = Helper::M('Material');
             $materialId = Helper::clean($this->getRequest()->getPost('material_id'));
             $materialName = Helper::clean($this->getRequest()->getPost('material_name'));
             $materialUrl = Helper::clean($this->getRequest()->getPost('material_url'));
             $adgroupId = Helper::clean($this->getRequest()->getPost('adgroup_id'));
             $adsizeId = Helper::clean($this->getRequest()->getPost('adsize_id'));
             $fileInfo = Helper::clean($this->getRequest()->getPost('fileinfo'));
             $adsizeId = explode(",", $adsizeId);
             $file_path = explode(",", $fileInfo);
             if (count($adsizeId) != count($file_path)) {
                 Helper::outJson(array('state' => 0, 'msg' => '图片和规格数量不匹配'));
             }
             if (empty($materialName) || empty($materialUrl)) {
                 Helper::outJson(array('state' => 0, 'msg' => '素材名称和跳转url不能为空'));
             }
             if (intval($adgroupId) == 0) {
                 Helper::outJson(array('state' => 0, 'msg' => '请选择素材类型'));
             }
             if(intval($materialId)==0){
                 Helper::outJson(array('state' => 0, 'msg' => '请选择您要修改的素材'));
             }
             $this->config = Yaf_Registry::get('config');
             $ACCESSORY_FOLDER = $this->config['config']['ACCESSORY_FOLDER'];
             //修改素材库
             $data['material_name'] = $materialName;
             $data['material_url'] = $materialUrl;
             $data['adgroup_id'] = $adgroupId;
             $data['comp_id'] = 0;//广告主ID
             $data['comp_pid'] = $this->uinfo['comp_id'];//服务商ID
             $data['edit_staff_id'] = $this->uinfo['staff_id'];//用户ID
             $data['edit_time'] = time();
             $res = $materialModel->updateMaterial($data, $materialId);

             if (!empty($res)) {
                 //修改了图片
                 $materialSizeInfo = $materialModel->getMaterialSizeInfo($materialId);
                 //改变了分组则删除原有的素材规格添加新的规格
                if($materialSizeInfo[0]['adgroup_id']!=$adgroupId){
                    foreach ($materialSizeInfo as $k => $v){
                        $url = str_replace($ACCESSORY_URL, $ACCESSORY_FOLDER, $v['img_url']);
                        if(file_exists($url)){
                            unlink($url);
                        };
                    }
                    $res = $materialModel->deleteMaterialSize($materialId);
                    if(!res){
                        $this->db->dsp->rollback();//100msh_ad广告数据库事务回滚
                        Helper::outJson(array('state' => 2, 'msg' => '修改失败'));
                    } else {
                        foreach ($adsizeId as $key => $value){
                            $materialSizeData['img_url'] = $file_path[$key];
                            $materialSizeData['material_id'] = $materialId;
                            $materialSizeData['ad_size_id'] = $adsizeId[$key];
                            $res = $materialModel->addMaterialSzie($materialSizeData);
                            if (!$res) {
                                $this->db->dsp->rollback();//100msh_ad广告数据库事务回滚
                                Helper::outJson(array('state' => 2, 'msg' => '修改失败'));
                                exit;
                            }
                        }
                    }
                  } else {
                    //没有改变分组则做更新操作
                    foreach ($materialSizeInfo as $key => $value){
                     if($value["img_url"]!=$file_path[$key]){
                         $url = str_replace($ACCESSORY_URL, $ACCESSORY_FOLDER, $value['img_url']);
                         if(file_exists($url)){
                             unlink($url);
                         };
                         $res = $materialModel->updateMaterialSize($value['ms_id'],$file_path[$key]);
                         if (!$res) {
                             $this->db->dsp->rollback();//100msh_ad广告数据库事务回滚
                             Helper::outJson(array('state' => 2, 'msg' => '修改失败'));
                             exit;
                         }
                     }
                    }
                }
                 //添加操作日志
                 $log_content[] = "素材管理 :" . "修改素材";
                 $log_content_str = implode(" , ", $log_content);
                 $log_obj = Helper::M('Log');
                 $log_obj->create_log('素材修改', $log_content_str, 'LC003', "LOT002");
                 $this->db->dsp->commit();//100msh_ad广告数据库事务提交
                 Helper::outJson(array('state' => 1, 'msg' => '修改成功'));
             } else {
                 $this->db->dsp->rollback();//100msh_ad广告数据库事务回滚
                 Helper::outJson(array('state' => 2, 'msg' => '修改失败'));
             }

         }

     }
     /**
      * 删除素材库
      */
     public function deleteAction(){
         Yaf_Dispatcher::getInstance()->disableView();
         $materialId = Helper::clean($this->getRequest()->getParam('id' , ''));
         $materialModel = Helper::M('Material');
         $res = $materialModel->deleteMaterial($materialId);
         $materialSizeInfo = $materialModel->getMaterialSizeInfo($materialId);
         //删除图片源文件
         foreach($materialSizeInfo as $k=>$v){
             $this->config = Yaf_Registry::get('config');
             $ACCESSORY_FOLDER=$this->config['config']['ACCESSORY_FOLDER'];
             $ACCESSORY_URL = $this->config['config']['ACCESSORY_URL'];
             $url = str_replace($ACCESSORY_URL,$ACCESSORY_FOLDER,$v['img_url']);
             if(file_exists($url)){
                 unlink($url);
             };

         }
         $result = $materialModel->deleteMaterialSize($materialId);
         if($res&&$result){

             //添加操作日志
             $log_content[] = "素材管理 :". "删除素材";
             $log_content_str = implode(" , ",$log_content);
             $log_obj = Helper::M('Log');
             $log_obj->create_log('素材删除',$log_content_str,'LC003',"LOT003");
             Helper::outJson(array('state' => 1, 'msg' => '删除成功'));
         } else {
             Helper::outJson(array('state' => 2, 'msg' => '删除失败'));
         }
     }


    /**
     * 上传图片
     */
    public function uploadFileAction(){

        $file = $_FILES;
        $sizeid = Helper::clean($this->getRequest()->getPost('sizeid'));
        if(empty($file)){
            Helper::outJson(array('state' => 0, 'msg' => '您没有选择任何文件'));
        }
        if(intval($sizeid)==0){
            Helper::outJson(array('state' => 0, 'msg' => '缺少图片规格'));
        }

        $ACCESSORY_FOLDER = $this->config['config']['ACCESSORY_FOLDER'];
        $materialModel = Helper::M("Material");

        //实际上传路径
        $nowday= date("Ymd",time());//今天的日期
        $nowmonth = date("Ym",time());
       // $UpFileDir = $ACCESSORY_FOLDER ."DspAdUpFile/";
        $UpFileDir = $ACCESSORY_FOLDER.$nowmonth."/".$nowday."/dsp/";
        $uploadObj = new Upload();
        $fileNamge = "file";
        $size = getimagesize($_FILES['file']['tmp_name']);
        //根据素材id获取图片尺寸
        $gsize = $materialModel->getSizeInfo($sizeid);
        $gsize_w = $gsize['width'];
        $gsize_h = $gsize['height'];
        if($size[0]!=$gsize_w||$size[1]!=$gsize_h){
            Helper::outJson(array('status'=>0,'msg'=>"图片上传失败:上传的图片规格不符合要求！请上传".$gsize_w."*".$gsize_h."px的文件！"));
        }
        $ext = strtolower(strrchr($_FILES["file"]["name"], "."));
        $uploadObj->UpFileAttribute($fileNamge);
        $UpFileName = "dsp-img-".$sizeid."-".time();
        $UpFileName= MD5($UpFileName);//无文件后缀
        $MaxSize = '500';
        $FileType = array('.png','.jpg','.jpeg','.bmp');//后缀注意带上.
        $info = $uploadObj->Uploads($fileNamge,$UpFileDir,$UpFileName,$MaxSize,$FileType,null);
        switch ($info){
            case -1:
                Helper::outJson(array('status'=>0,'msg'=>"图片上传失败:上传的图片类型不正确！请上传类型为png,jpg,jpeg,bmp的文件！"));
                break;
            case -2:
                Helper::outJson(array('status'=>0,'msg'=>"图片上传失败:上传的图片大小不符合要求！请上传小于".$MaxSize."(KB)的文件！"));
                break;
            default:
                $ACCESSORY_URL = $this->config['config']['ACCESSORY_URL'];
                sleep(2);
                $file_path = $ACCESSORY_URL.$nowmonth."/".$nowday."/dsp/". $UpFileName.$ext;
                Helper::outJson(array('state'=>1,'msg'=>'图片上传成功','url'=>$file_path));
                break;
        }

    }
    /**
     * 上传图片
     */
    public function uploadBhFileAction()
    {

        $file = $_FILES;
        $ACCESSORY_FOLDER = $this->config['config']['ACCESSORY_FOLDER'];
        $materialModel = Helper::M("Material");

        //实际上传路径
        $nowday = date("Ymd", time());//今天的日期
        $nowmonth = date("Ym", time());
        // $UpFileDir = $ACCESSORY_FOLDER ."DspAdUpFile/";
        $UpFileDir = $ACCESSORY_FOLDER . $nowmonth . "/" . $nowday . "/dsp/";
        $uploadObj = new Upload();
        $fileNamge = "file";
        $size = getimagesize($_FILES['file']['tmp_name']);
        //根据素材id获取图片尺寸

        $ext = strtolower(strrchr($_FILES["file"]["name"], "."));
        $uploadObj->UpFileAttribute($fileNamge);
        $UpFileName = "dsp-img-" . time();
        $UpFileName = MD5($UpFileName);//无文件后缀
        $MaxSize = '500';
        $FileType = array('.png', '.jpg', '.jpeg', '.bmp');//后缀注意带上.
        $info = $uploadObj->Uploads($fileNamge, $UpFileDir, $UpFileName, $MaxSize, $FileType, null);
        switch ($info) {
            case -1:
                Helper::outJson(array('status' => 0, 'msg' => "图片上传失败:上传的图片类型不正确！请上传类型为png,jpg,jpeg,bmp的文件！"));
                break;
            case -2:
                Helper::outJson(array('status' => 0, 'msg' => "图片上传失败:上传的图片大小不符合要求！请上传小于" . $MaxSize . "(KB)的文件！"));
                break;
            default:
                $ACCESSORY_URL = $this->config['config']['ACCESSORY_URL'];
                sleep(2);
                $file_path = $ACCESSORY_URL . $nowmonth . "/" . $nowday . "/dsp/" . $UpFileName . $ext;
                Helper::outJson(array('state' => 1, 'msg' => '图片上传成功', 'url' => $file_path));
                break;
        }
    }

        /**
     * 上传图片
     */
    public function uploadFileJrAction(){

        $file = $_FILES;


        $ACCESSORY_FOLDER = $this->config['config']['ACCESSORY_FOLDER'];
        $materialModel = Helper::M("Material");

        //实际上传路径
        $nowday= date("Ymd",time());//今天的日期
        $nowmonth = date("Ym",time());
        // $UpFileDir = $ACCESSORY_FOLDER ."DspAdUpFile/";
        $UpFileDir = $ACCESSORY_FOLDER.$nowmonth."/".$nowday."/dsp/";
        $uploadObj = new Upload();
        $fileNamge = "file";

        $ext = strtolower(strrchr($_FILES["file"]["name"], "."));
        $uploadObj->UpFileAttribute($fileNamge);
        $UpFileName = "dsp-img-".time();
        $UpFileName= MD5($UpFileName);//无文件后缀
        $MaxSize = '500';
        $FileType = array('.png','.jpg','.jpeg','.bmp');//后缀注意带上.
        $info = $uploadObj->Uploads($fileNamge,$UpFileDir,$UpFileName,$MaxSize,$FileType,null);
        switch ($info){
            case -2:
                Helper::outJson(array('status'=>0,'msg'=>"图片上传失败:上传的图片大小不符合要求！请上传小于".$MaxSize."(KB)的文件！"));
                break;
            default:
                $ACCESSORY_URL = $this->config['config']['ACCESSORY_URL'];
                sleep(2);
                $file_path = $ACCESSORY_URL.$nowmonth."/".$nowday."/dsp/". $UpFileName.$ext;
                Helper::outJson(array('state'=>1,'msg'=>'图片上传成功','url'=>$file_path));
                break;
        }


    }



     /**
      * 素材分组
      */
     public function getGroupListAction(){
         $materialModel = Helper::M('Material');
         $groupList = $materialModel->adgroupList();
         Helper::outJson(array('state'=>1,'msg'=>'','data'=>$groupList));
     }
     /**
      * 素材分组及规格对应
      */
     public function getGroupSizeAction(){
         Yaf_Dispatcher::getInstance()->disableView();
         $materialModel = Helper::M('Material');
         $groupSizeList = $materialModel->getAdgroupList();
         Helper::outJson(array('state'=>1,'msg'=>'','data'=>$groupSizeList));
     }
    /*
     * 上传文件
     */
    //上传文件
    public function uploadsmsAction(){
        set_time_limit(0);
        $upload_obj = new Upload();
        $ACCESSORY_FOLDER = $this->config['config']['ACCESSORY_FOLDER'];
        $nowday= date("Ymd",time());//今天的日期
        $nowmonth = date("Ym",time());
        //得到后缀
        $ext = strtolower(strrchr($_FILES['file']['name'], "."));
        $file_name = md5(uniqid(mt_rand(), true).time());
        $file_name_ext = $file_name . $ext;
        $type = array('.txt');
        $Max_size = 500;
        $mypath = $ACCESSORY_FOLDER.$nowmonth."/".$nowday."/sms/";
        $result = $upload_obj->Uploads('file', $mypath, $file_name, $Max_size, $type);
        if (!$result) {
            $arr = array('api_code' => 0, 'msg' => '文件上传错误');
            echo json_encode($arr);    exit;
        }else{
            $file_path=$mypath.$file_name.$ext;
            $content=file_get_contents($file_path);
            $mobile_arr=explode("\r\n",$content);
            $phones = array();
            $phone_content = "";
            $file_z_num = 0;
            $remove_num = 0;
            $y_num = 0;
            foreach($mobile_arr as $key=>$val){
                if(!empty($val)){
                    $file_z_num++;
                    if(!preg_match("/^1[34578]\d{9}$/", $val)){
                        $y_num++;
                    }else{
                        if(!in_array($val,$phones)){
                            $phones[] = $val;
                            if($phone_content){
                                $phone_content = $phone_content.",".$val;
                            }else{
                                $phone_content = $val;
                            }
                        }else{
                            $remove_num++;
                        }
                    }
                    $temp[]=trim($val);
                }
            }

            if($file_z_num>10000){
                $arr = array('api_code' => 0, 'msg' => '单次上传手机号过多，每次最多只能上传1万手机号!');
                echo json_encode($arr);    exit;
            }else{
                $use_num = $file_z_num-$remove_num-$y_num;
                if(0>=$use_num){
                    $arr = array('api_code' => 0, 'msg' => '文件里号码为空，请检查后重新上传！');
                    echo json_encode($arr);    exit;
                }
                /*
                 * 进行数据上传
                 */
                $materialModel = Helper::M('Material');
                $par['file_url'] = $file_path;
                $par['file_z_num'] = $file_z_num;
                $par['remove_num'] = $remove_num;
                $par['y_num'] = $y_num;
                $par['phone_content'] = $phone_content;
                $market_file_id = $materialModel->addfile($par);
                $arr = array('api_code' => 1, 'msg' => 'success');
                $arr['data'] = array('market_file_id'=>$market_file_id,'file_z_num'=>$file_z_num,'remove_num'=>$remove_num,'y_num'=>$y_num);
                echo json_encode($arr);exit;
            }

        }
    }

}