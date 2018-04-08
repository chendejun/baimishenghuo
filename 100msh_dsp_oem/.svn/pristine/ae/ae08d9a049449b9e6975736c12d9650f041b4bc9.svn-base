<?php

/**
 *公众号
 */
class OfficialAccountController extends BasicController
{
    /**
     * [createAction 绑定公众号]
     * @return [json] [绑定执行结果]
     */
    public function createAction()
    {
        // 是否post请求
        if (isset($_POST) && !empty($_POST)) {
            // 定义图片要求
            $logoLimit=array('size'=>50,'width'=>114,'height'=>114,'ext'=>array('jpg','jpeg','png'));
		    $imageLimit=array('size'=>200,'width'=>344,'height'=>344,'ext'=>array('jpg','jpeg','png'));
            $uinfo = $_SESSION['uinfo'];
            //得到请求参数。
            $pn_name = Helper::clean($this->getRequest()->getPost('pn_name'));
            $pn_type = Helper::clean($this->getRequest()->getPost('pn_type'));
            $pn_number = Helper::clean($this->getRequest()->getPost('pn_number'));
            $shop_name = Helper::clean($this->getRequest()->getPost('shop_name'));
            $pn_appid = Helper::clean($this->getRequest()->getPost('pn_appid'));
            $shop_id = Helper::clean($this->getRequest()->getPost('shop_id'));
            $ssid = Helper::clean($this->getRequest()->getPost('ssid'));
            $pn_appsecret = Helper::clean($this->getRequest()->getPost('pn_appsecret'));
            $secret_key = Helper::clean($this->getRequest()->getPost('secret_key'));
            $mobile_decrypt_key = Helper::clean($this->getRequest()->getPost('mobile_decrypt_key',0));
            $biz_code = Helper::clean($this->getRequest()->getPost('biz_code'));
            $public_cate = Helper::clean($this->getRequest()->getPost('public_cate'));

            // 表单验证
            if (empty($pn_name)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入公众号名称!'));
            }
            if (empty($pn_type)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入公众号类型!'));
            }
            if (empty($pn_number)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入微信号!'));
            }
            if (empty($shop_name)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入门店名称!'));
            }
            if (empty($pn_appid)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入Appid!'));
            }
            if (empty($shop_id)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入shopid!'));
            }
            if (empty($ssid)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入ssid!'));
            }
            if (empty($pn_appsecret)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入Appsecret!'));
            }
            if (empty($secret_key)){
                Helper::outJson(array('status' => 0 , 'msg' => '请输入secretkey!'));
            }
            if (empty($public_cate)){
                Helper::outJson(array('status' => 0 , 'msg' => '请选择运营类别!'));
            }
            if (empty($_FILES["logo"]['name'])){
                Helper::outJson(array('status' => 0 , 'msg' => '请上传logo!'));
            }

            // 检查APPID、appSecret、微信号的唯一性
            $official_accounts = Helper::M('OfficialAccounts');
            $result_pn_appid = $official_accounts->selectOfficialAccounts('pn_appid="'.$pn_appid.'"');
            if ($result_pn_appid) {
                Helper::outJson(array('state' => 0 , 'msg' => 'Appid已经存在!'));
            }
            $result_pn_appsecret = $official_accounts->selectOfficialAccounts('pn_appsecret="'.$pn_appsecret.'"');
            if ($result_pn_appid) {
                Helper::outJson(array('state' => 0 , 'msg' => 'appSecret已经存在!'));
            }
            $result_pn_number = $official_accounts->selectOfficialAccounts('pn_number="'.$pn_number.'"');
            if ($result_pn_appid) {
                Helper::outJson(array('state' => 0 , 'msg' => '微信公众号已经存在!'));
            }

            // 检测手机号解密秘钥的的正确性
            if ($mobile_decrypt_key) {
                $this->checkMobileKey($mobile_decrypt_key);
            }

            // 检查appid，secretKety，shopId的正确性
            preg_match("/^[0-9a-zA-Z]+$/", $pn_appid, $appidMatch);
    		preg_match("/^[0-9a-zA-Z]+$/", $secret_key, $secretMatch);
    		if (empty($appidMatch) || empty($secretMatch)) {
    			Helper::outJson(array("state"=>0,"msg"=>"Appid和secretKety只能填字母和数字！"));
    		}
    		if (!is_numeric($shop_id)) {
    			Helper::outJson(array("state"=>0,"msg"=>"shopId只能填数字！"));
    		}

            //上传logo、二维码
            $upload_obj = new Upload();

            // 检查图片合法性
            $logo_info = getimagesize($_FILES["logo"]['tmp_name']);
			if(($logo_info[0]!=$logoLimit['width'])||($logo_info[1]!=$logoLimit['height'])){
				Helper::outJson(array('state'=>0,'msg'=>"logo上传失败:上传的图片尺寸不符合要求！请上传尺寸为".$logoLimit['width']."*".$logoLimit['height']."像素(px)的文件！"));
			}

            $logo_ext = $this->checkSuffixName($_FILES["logo"]['name'],$logoLimit['ext']);

            if (!$logo_ext) {
                Helper::outJson(array('state'=>0,'msg'=>"Logo上传失败:上传的图片类型不正确！请上传.jpg,.jpeg,.png 的文件！"));exit;
            }

            $config = Yaf_Registry::get("config");
            $moth = date('Ym',time());
            $UpFileDir = $config['config.ACCESSORY_FOLDER'].'weixin/'.$moth.'/';
            $UpFileName = uniqid().'_'.time();

            // 上传
            $upload_log_res = $upload_obj->Uploads('logo',$UpFileDir,$UpFileName,$logoLimit['size']);

            switch($upload_log_res){
    			case -3:
    				Helper::outJson(array('state'=>0,'msg'=>"Logo上传失败"));exit;
    				break;
    			default:
    				break;
		    }

            $new_logo = 'weixin/'.$moth.'/'.$UpFileName.'.'.$logo_ext;
            $new_images = '';

            //上传二维码
            if (!empty($_FILES['pn_image']['name'])) {
                // 检查图片合法性
                $logo_info = getimagesize($_FILES["pn_image"]['tmp_name']);
    			if(($logo_info[0]!=$imageLimit['width'])||($logo_info[1]!=$imageLimit['height'])){
    				Helper::outJson(array('state'=>0,'msg'=>"二维码上传失败:上传的图片尺寸不符合要求！请上传尺寸为".$imageLimit['width']."*".$imageLimit['height']."像素(px)的文件！"));
    			}

                $image_ext = $this->checkSuffixName($_FILES["pn_image"]['name'],$imageLimit['ext']);

                if (!$image_ext) {
                    Helper::outJson(array('state'=>0,'msg'=>"二维码上传失败:上传的图片类型不正确！请上传.jpg,.jpeg,.png 的文件！"));exit;
                }

                $config = Yaf_Registry::get("config");
                $UpFileDir = $config['config.ACCESSORY_FOLDER'].'weixin/'.$moth.'/';
                $UpFileName = uniqid().'_'.time();
                // 上传
                $upload_img_res = $upload_obj->Uploads('pn_image',$UpFileDir,$UpFileName,$imageLimit['size']);

                switch($upload_img_res){
        			case -3:
        				Helper::outJson(array('state'=>0,'msg'=>"二维码上传失败"));exit;
        				break;
        			default:
        				break;
    		    }
                $new_images = 'weixin/'.$moth.'/'.$UpFileName.'.'.$image_ext;

            }

            // 插入公众号
            $data = [
                'pn_name'=>$pn_name,
                'pn_appid'=>$pn_appid,
                'pn_appsecret'=>$pn_appsecret,
                'pn_number'=>$pn_number,
                'mobile_decrypt_key'=>$mobile_decrypt_key,
				'pn_logo'=>$new_logo,
                'pn_qrcode'=>$new_images,
                'pn_add_time'=>time(),
                'pn_add_uid'=>$uinfo['staff_id'],
                'pn_edit_time'=>0,
                'pn_edit_uid'=>0,
                'is_count'=>1,
                'comp_id'=>$uinfo['comp_id'],
				'public_type'=>$pn_type,
				'public_cate'=>$public_cate,
				'biz_code'=>$biz_code
            ];
            $shop_data = [
                'shop_name'=>$shop_name,
                'app_id'=>$pn_appid,
                'shop_id'=>$shop_id,
                'ssid'=>$ssid,
                'secret_key'=>$secret_key,
                'add_date'=>date('Y-m-d H:i:s',time())
            ];
            $official_accounts = Helper::M('OfficialAccounts');
            $message = $official_accounts->addOfficialAccount($data,$shop_data);
            if ($message) {
                Helper::outJson(array('state'=>1,'msg'=>"绑定公众号成功"));
            }else{
                Helper::outJson(array('state'=>0,'msg'=>"绑定失败"));
            }
        }

    }

    /**
     * [checkSuffixName 检查文件后缀是否是指定的]
     * @param  [type] $file_name   [文件名]
     * @param  [type] $check_array [指定后缀]
     * @return [mixed]              [成功返回后缀]
     */
    protected function checkSuffixName($file_name,$check_array)
    {

        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (in_array($file_ext,$check_array)) {
            return $file_ext;
        }else{
            return false;
        }

    }


    /**
     * [checkMobileKey 检验手机号秘钥的正确性]
     * @param  [type] $mobile_decrypt_key [手机号解密秘钥]
     * @return [string]                     []
     */
    protected function checkMobileKey($mobile_decrypt_key)
    {
        if(!empty($mobile_decrypt_key)){
                if(strpos($mobile_decrypt_key, "|") !== FALSE){
                    $mobile_decrypt_key_temp = explode("|", $mobile_decrypt_key);
                    if(count($mobile_decrypt_key_temp) > 0){
                        $mobile_decrypt_key = "";
                        foreach ($mobile_decrypt_key_temp as $row){
                            if(ctype_alnum($row)){
                                $mobile_decrypt_key .= trim($row).'|';
                            } else {
                                Helper::outJson(array('state'=>0,'msg'=>'秘钥串只能由数字和字母组成'));exit;
                            }
                        }
                        $mobile_decrypt_key = rtrim($mobile_decrypt_key,'|');
                        unset($mobile_decrypt_key_temp);
                    } else {
                        Helper::outJson(array('state'=>0,'msg'=>'手机号解密秘钥必须由多个秘钥串组成并以"|"作为分隔符隔开'));exit;
                    }
                } else {
                    Helper::outJson(array('state'=>0,'msg'=>'手机号解密秘钥必须由多个秘钥串组成并以"|"作为分隔符隔开'));exit;
                }
            }
    }

    public function testAction(){}
    public function indexpageAction(){}
    public function createpageAction(){}
    public function viewpageAction(){}


    /**
     * [indexAction 所有公众号]
     * @return [json] [所有公众号的json结果集]
     */
    public function indexAction()
    {

        $uinfo = $_SESSION['uinfo'];
        $page = Helper::clean($this->getRequest()->getParam('page',1));
        $pagecount = Helper::clean($this->getRequest()->getParam('pagecount',10));
        $start=($page-1)*$pagecount;
        $official_accounts = Helper::M('OfficialAccounts');
        $rel = $official_accounts->selectAllOfficialAccount($start ,$pagecount);
        // 得到总记录数
        $num = $this->getCount();

        Helper::outJson(array('state'=>1,'data'=>$rel,'totalcount'=>$num,'pageindex'=>$page,'pagecount'=>$pagecount));
    }

    /**
     * [getCount 得到公众号数量]
     * @return [type] [description]
     */
    protected function getCount()
    {
        $official_accounts = Helper::M('OfficialAccounts');
        $num = $official_accounts->officialAccountCount();
        return $num;

    }


    public function getAllListAction()
    {
        $official_accounts = Helper::M('OfficialAccounts');
        $rel = $official_accounts->officialAccountList();
        Helper::outJson(array('state'=>1,'data'=>$rel));
    }

    public function delAction()
    {
        if (isset($_POST) && !empty($_POST)) {
            $pn_id = Helper::clean($this->getRequest()->getPost('pn_id'));
            if (empty($pn_id)){
                Helper::outJson(array('state' => 0 , 'msg' => '请设置pn_id!'));
            }
            $official_accounts = Helper::M('OfficialAccounts');
            // 判断该公众号是否有投递计划
            if ($official_accounts->getPlanId($pn_id)) {
                Helper::outJson(array('state' => 0 , 'msg' => '不符合解绑条件，只有未投递计划的公众号才能解绑!'));
            }

            // 解绑
            $id = $official_accounts->del($pn_id);
            if ($id) {
                Helper::outJson(array('state' => 1 , 'msg' => '解绑成功!'));
            }else {
                Helper::outJson(array('state' => 0 , 'msg' => '解绑失败!'));
            }

        }else{
            Helper::outJson(array('state'=>0,'msg'=>'非法请求'));
        }

    }

    /**
     * [getInfo 得到公众号详情]
     * @return [type] [description]
     */
    public function getInfoAction()
    {
        $pn_id = Helper::clean($this->getRequest()->getParam('pn_id',0));
        if (empty($pn_id)){
            Helper::outJson(array('state' => 0 , 'msg' => '请设置pn_id!'));
        }

        $official_accounts = Helper::M('OfficialAccounts');

        $data = $official_accounts->getInfo($pn_id);

        Helper::outJson(array('state'=>1,'data'=>$data));


    }











}
