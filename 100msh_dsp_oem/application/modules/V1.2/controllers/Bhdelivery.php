<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/22
 * Time: 18:02
 */

class BhdeliveryController extends BasicController {


    public function addviewAction(){

    }
    public function addview1Action(){

    }
    public function addview2Action(){

    }
    public function addview3Action(){

    }
    public function addview4Action(){

    }
    public function addview5Action(){

    }
    public function indexviewAction(){

    }
    public function getaccess(){
        $this->config = Yaf_Registry::get('config');
        $token_url = $this->config['dsp']['token_url'];//token��url
        $result =  json_decode(file_get_contents($token_url),true);
        $access_token = $result['data']['access_token'];
        return  $access_token;
    }
	/**
	 * skylon
	 * ��ȡ�����̹�����б�
	 **/
    public function getCompListAction(){
    	$name = Helper::clean($this->getRequest()->getPost('name'));
    	$accountModel = Helper::M('Account');
    	$account_ids=$accountModel->getComp_ids();
    	$param = [
            'comp_pid'=>$this->uinfo['comp_id'],
            'name'=>$name,
    		'comp_id'=>$account_ids
        ];
        $result = BmDspApi::request('bhad/getCompList', $param);
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('api_code'=>1,'data'=>$result['data']));
        }
        Helper::outJson(array('api_code'=>0,'data'=>''));
    	
    	
    	
        $name = Helper::clean($this->getRequest()->getParam('name' , ''));
        $comp_pid = $this->uinfo['comp_id'];
        $access_token = $this->getTokenAction();
    	$accountModel = Helper::M('Account');
    	$account_ids=$accountModel->getComp_ids();
    	if(!empty($account_ids)){
    		$url = $this->config['dsp']['url']."delivery/getCompList?access_token=$access_token&comp_pid=$comp_pid&name=$name&comp_id=$account_ids";
    	}else{
    		$url = $this->config['dsp']['url']."delivery/getCompList?access_token=$access_token&comp_pid=$comp_pid&name=$name";
    	}
        $api_res = DoNetwork::makeRequest($url);
        $api_res = json_decode($api_res,true);
        if($api_res['api_code']==0){
            Helper::outJson(array('state'=>1,'msg'=>'','data'=>$api_res['data']));
        }else {
            Helper::outJson(array('state'=>2,'msg'=>'����ʧ��'));
        }

    }
    /*
     * ��ȡ���ƻ��б�
     */
	public function getCampaignListAction(){
		$name = Helper::clean($this->getRequest()->getPost('name'));
        $start_date = Helper::clean($this->getRequest()->getPost('start_date'));
        $end_date = Helper::clean($this->getRequest()->getPost('end_date'));
        $status = Helper::clean($this->getRequest()->getPost('status'));
        $page = Helper::clean($this->getRequest()->getPost('page',1));
        $param = [
        	'platform_id'=>5,
            'comp_pid'=>$this->uinfo['comp_id'],
            'name'=>$name,
        	'status'=>$status,
            'page'=>$page,
            'start_date'=>$start_date,
            'end_date'=>$end_date
        ];
        $result = BmDspApi::request('bhad/getCampaignList', $param);
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('api_code'=>1,'data'=>$result['data']));
        }
        Helper::outJson(array('api_code'=>0,'data'=>''));
    }
	/*
     * ��ȡ���ƻ��б�
     */
	public function getCompCampaignListAction(){
		$comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));
        $param = [
        	'platform_id'=>5,
        	'type'=>2,
            'comp_pid'=>$this->uinfo['comp_id'],
            'comp_id'=>$comp_id
        ];
        $result = BmDspApi::request('bhad/getCampaignList', $param);
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('api_code'=>1,'data'=>$result['data']));
        }
        Helper::outJson(array('api_code'=>0,'data'=>''));
    }
	/*
     * ��ȡ���ƻ���Ͷ���б�
     */
	public function getDeliveryListAction(){
        $bh_campaign_id = Helper::clean($this->getRequest()->getPost('bh_campaign_id'));
        $result = BmDspApi::request('bhad/getDeliveryList', array('bh_campaign_id'=>$bh_campaign_id));
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('api_code'=>1,'data'=>$result['data']));
        }
        Helper::outJson(array('api_code'=>0,'data'=>$result['error']));
	}
	/*
     * ��ȡ���Ͷ����ϸ��Ϣ
     */
	public function getDeliveryInfoAction(){
        $bh_delivery_id = Helper::clean($this->getRequest()->getPost('bh_delivery_id'));
        $result = BmDspApi::request('bhad/getDeliveryInfo', array('bh_delivery_id'=>$bh_delivery_id));
        if (!empty($result) && $result['api_code'] == 0 && isset($result['data'])) {
            Helper::outJson(array('api_code'=>1,'data'=>$result['data']));
        }
        Helper::outJson(array('api_code'=>0,'data'=>$result['error']));
	}
      /*
       * ���ƻ������Ƿ��Ѿ�ʹ��
       */
      public function checkCamAction(){
          $bh_campaign_name = Helper::clean($this->getRequest()->getPost('bh_campaign_name'));//���ƻ�����
          $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//�����id
          $Bhdelivery = Helper::M('Bhdelivery');
          $result = $Bhdelivery->checkCam($comp_id,$bh_campaign_name);
          if($result){
              $arr = array('api_code' => 0, 'msg' => '�üƻ������Ѿ���ʹ��');
              Helper::outJson($arr);
          }else{
              $arr = array('api_code' => 1, 'msg' => '�üƻ����ƿ���');
              Helper::outJson($arr);
          }

      }
    /*
   * ����������Ƿ��Ѿ�ʹ��
   */
    public function checkAdnameAction(){
        $delivery_name = Helper::clean($this->getRequest()->getPost('delivery_name'));//���ƻ�����
        $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//�����id
        $Bhdelivery = Helper::M('Bhdelivery');
        $result = $Bhdelivery->checkAdname($comp_id,$delivery_name);
        if($result){
            $arr = array('api_code' => 0, 'msg' => '�ù�������Ѿ���ʹ��');
            Helper::outJson($arr);
        }else{
              $arr = array('api_code' => 1, 'msg' => '�ù��������δ��ʹ��');
              Helper::outJson($arr);
          }

    }
      /*
       *��Ӽƻ�
       */
      public function addCampAction(){
          $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//�����id
          $bh_campaign_name = Helper::clean($this->getRequest()->getPost('bh_campaign_name'));//���ƻ�����
          $startDate = Helper::clean($this->getRequest()->getPost('startDate'));//��ʼʱ��
          $endDate = Helper::clean($this->getRequest()->getPost('endDate'));//����ʱ��
          $budgetSwitch = Helper::clean($this->getRequest()->getPost('budgetSwitch'));//��Ԥ��״̬
          $budget = Helper::clean($this->getRequest()->getPost('budget'));//��Ԥ��
          $totalBudgetSwitch = Helper::clean($this->getRequest()->getPost('totalBudgetSwitch'));//��Ԥ��״̬
          $totalBudget = Helper::clean($this->getRequest()->getPost('totalBudget'));//��Ԥ��
          $data['comp_id'] = $comp_id;
          $data['bh_campaign_name'] = $bh_campaign_name;
          $data['startDate'] = $startDate;
          $data['endDate'] = $endDate;
          $data['budgetSwitch'] = $budgetSwitch;
          $data['budget'] = $budget*100;
          $data['totalBudgetSwitch'] = $totalBudgetSwitch;
          $data['totalBudget'] = $totalBudget*100;
          $data['comp_pid'] = $this->uinfo['comp_id'];//������id
          $data['add_staff_id'] = $this->uinfo['staff_id'];
          $data['add_time'] = time();
          $this->db    = Yaf_Registry::get('db');
          $this->db->bh->startTrans();
          $Bhdelivery = Helper::M('Bhdelivery');
          $check_name = $Bhdelivery->checkCam($comp_id,$bh_campaign_name);
          if($check_name){
              $arr = array('api_code' => 0, 'msg' => '�����ѱ�ʹ��');
              Helper::outJson($arr);
          }
          $bh_campaign_id = $Bhdelivery->addcamp($data);
          if($bh_campaign_id) {
              //�����ʽ�
              $rand = rand(100,10000);
              $info = array('comp_id' => $comp_id, 'comp_pid' =>  $data['comp_pid'], 'amount' => $data['totalBudget'], 'rel_no' => 'bh_'.$rand.$bh_campaign_id);

              $detail = array(
                  array('platform_id' => 5, 'amount' => $data['totalBudget'])
              );
              // $freezeid =  $m->freezeAmount($info , $detail);

              $result = BmDspApi::request('fund/freezeAmount', array('info' => $info, 'data' => $detail));
              if (empty($result) || isset($result['error_code'])) {
                  $this->db->bh->rollback();
                  Helper::outJson(array('api_code' => 0, 'msg' => '���ʧ�ܣ��ʽ��쳣'));
                  exit;
              } else {
                  $freeze_id =  $result['data']['freeze_id'];
                  $update_date['freeze_id'] =$freeze_id;
                  $result1  = $Bhdelivery->edit($bh_campaign_id,$update_date);
                  if($result1){
                      $this->db->bh->commit();
                      $arr = array('api_code' => 1, 'msg' => 'success');
                      $arr['data'] = $bh_campaign_id;
                      Helper::outJson($arr);
                  }else{
                      $this->db->bh->rollback();
                      Helper::outJson(array('api_code' => 0, 'msg' => '���ʧ��'));
                  }
              }
          }else{
              Helper::outJson(array('api_code' => 0, 'msg' => '���ʧ��'));
              exit;
          }


      }

    /*
   * ��ȡ��Ⱥ���б�
   */
    public function getCrowdlistAction()
    {
        $comp_pid = $this->uinfo['comp_id'];//������id
        $Bhdelivery = Helper::M('Bhdelivery');
        $result = $Bhdelivery->getCrowdlist($comp_pid);
        if($result){
            foreach($result as $key=>$val){
                $Markete = Helper::M('Market');
                $crowd_info = $Markete->getCrowdinfo($val['crowd_id'], $comp_pid);;
                if($crowd_info){
                    $result[$key]['cover_users'] = $crowd_info[0]['cover_users'];
                }else{
                    $result[$key]['cover_users'] = '';
                }
            }
        }
        $arr = array('api_code' => 1, 'msg' => 'success',);
        $arr['data'] = $result;
        Helper::outJson($arr);
    }
    /*
     * ��ȡ����ý���б�
     */
    public function  getAlladxAction(){
        $Bhdelivery = Helper::M('Bhdelivery');
        $adxs_info = $Bhdelivery->getAlladx();
        $arr = array('api_code' => 1, 'msg' => 'success',);
        $arr['data'] = $adxs_info;
        Helper::outJson($arr);
    }

    /*
     * ��ȡý���б�
     */
    public function getAdxsAction(){

        $where  = "";
        $osType_o = Helper::clean($this->getRequest()->getPost('osType_o'));//�Ƿ�֧�ְ�׿
        if($osType_o==1){
            if($where){
                $where = $where." AND osType_o=1";
            }else{
                $where = "osType_o=1";

            }
        }
        $osType_i = Helper::clean($this->getRequest()->getPost('osType_i'));//�Ƿ�֧��ios
        if($osType_i==1){
            if($where){
                $where = $where." AND osType_i=1";
            }else{
                $where = "osType_i=1";
            }
        }
        $behavior_type_t = Helper::clean($this->getRequest()->getPost('behavior_type_t'));//�Ƿ�֧����ҳ��ת
        if($behavior_type_t==1){
            if($where){
                $where = $where." AND behavior_type_t=1";
            }else{
                $where = "behavior_type_t=1";
            }
        }
        $behavior_type_x = Helper::clean($this->getRequest()->getPost('behavior_type_x'));//�Ƿ�֧������Ӧ��
        if($behavior_type_x==1){
            if($where){
                $where = $where." AND behavior_type_x=1";
            }else{
                $where = "behavior_type_x=1";
            }
        }
        $action_type_h = Helper::clean($this->getRequest()->getPost('action_type_h'));//��ʽ֧��-��� 0 ��֧�� 1֧��
        if($action_type_h==1){
            if($where){
                $where = $where." AND action_type_h=1";
            }else{
                $where = "action_type_h=1";
            }
        }
       // $action_type_s = Helper::clean($this->getRequest()->getPost('action_type_s'));//��ʽ֧��-��Ƶ��Ƭ 0 ��֧�� 1֧��
        //$action_type_y = Helper::clean($this->getRequest()->getPost('action_type_y'));//��ʽ֧��-ԭ����Ϣ�� 0 ��֧�� 1֧��
        $action_type_k = Helper::clean($this->getRequest()->getPost('action_type_k'));//��ʽ֧��-���� 0 ��֧�� 1֧��
        if($action_type_k==1){
            if($where){
                $where = $where." AND action_type_k=1";
            }else{
                $where = "action_type_k=1";
            }
        }
        $source_type_q = Helper::clean($this->getRequest()->getPost('source_type_q'));//��Դ֧��-ȫ�������Ż� 0 ��֧�� 1֧��
        if($source_type_q==1){
            if($where){
                $where = $where." AND source_type_q=1";
            }else{
                $where = "source_type_q=1";
            }
        }
        $source_type_z = Helper::clean($this->getRequest()->getPost('source_type_z'));//��Դ֧��-ָ��Ӧ�� 0 ��֧�� 1֧��
        if($source_type_z==1){
            if($where){
                $where = $where." AND source_type_z=1";
            }else{
                $where = "source_type_z=1";
            }
        }
        $Bhdelivery = Helper::M('Bhdelivery');
        $size_info = $Bhdelivery->getAdxs($where);
        $adx_list = array();
        if($size_info){
                foreach($size_info as $key=>$val){
                          if(!in_array($val['adx_Id'],$adx_list)){
                              $adx_list[] = (int)$val['adx_Id'];
                          }
                }
        }
        if($adx_list){
            $adx_lists = "(";
            foreach ($adx_list as $key=>$val){
                if($adx_lists== "("){
                    $adx_lists = $adx_lists.$val;
                }else{
                    $adx_lists = $adx_lists.",".$val;
                }


            }
            $adx_lists = $adx_lists.")";
            //��ȡý���б�
          $adxInfo =   $Bhdelivery->getAdxlist($adx_lists);
        }else{
            $adxInfo = '';
        }
        $arr = array('api_code' => 1, 'msg' => 'success');
        $arr['data'] = $adxInfo;
        Helper::outJson($arr);

    }
    /*
     * ��ȡ��Ӧ��ý���ѡ����б�
     */
    public function getSourcestypeqAction(){
        $adx_ids = Helper::clean($this->getRequest()->getPost('adx_ids'));//ý���б�
        $adxs_arr = explode(",",$adx_ids);
        $list_arr = array();
        $lists_arr = array();
        foreach ($adxs_arr as $key=>$val){
            $where  = "";
            $adx_id = $val;//ý���б�
            if($adx_id){
                if($where){
                    $where = $where." AND $adx_id=".$adx_id;
                }else{
                    $where = "$adx_id=".$adx_id;;
                }
            }
            $osType_o = Helper::clean($this->getRequest()->getPost('osType_o'));//�Ƿ�֧�ְ�׿
            if($osType_o==1){
                if($where){
                    $where = $where." AND osType_o=1";
                }else{
                    $where = "osType_o=1";
                }
            }
            $osType_i = Helper::clean($this->getRequest()->getPost('osType_i'));//�Ƿ�֧��ios
            if($osType_i==1){
                if($where){
                    $where = $where." AND osType_i=1";
                }else{
                    $where = "osType_i=1";
                }
            }
            $behavior_type_t = Helper::clean($this->getRequest()->getPost('behavior_type_t'));//�Ƿ�֧����ҳ��ת
            if($behavior_type_t==1){
                if($where){
                    $where = $where." AND behavior_type_t=1";
                }else{
                    $where = "behavior_type_t=1";
                }
            }
            $behavior_type_x = Helper::clean($this->getRequest()->getPost('behavior_type_x'));//�Ƿ�֧������Ӧ��
            if($behavior_type_x==1){
                if($where){
                    $where = $where." AND behavior_type_x=1";
                }else{
                    $where = "behavior_type_x=1";
                }
            }
            $action_type_h = Helper::clean($this->getRequest()->getPost('action_type_h'));//��ʽ֧��-��� 0 ��֧�� 1֧��
            if($action_type_h==1){
                if($where){
                    $where = $where." AND action_type_h=1";
                }else{
                    $where = "action_type_h=1";
                }
            }
            // $action_type_s = Helper::clean($this->getRequest()->getPost('action_type_s'));//��ʽ֧��-��Ƶ��Ƭ 0 ��֧�� 1֧��
            //$action_type_y = Helper::clean($this->getRequest()->getPost('action_type_y'));//��ʽ֧��-ԭ����Ϣ�� 0 ��֧�� 1֧��
            $action_type_k = Helper::clean($this->getRequest()->getPost('action_type_k'));//��ʽ֧��-���� 0 ��֧�� 1֧��
            if($action_type_k==1){
                if($where){
                    $where = $where." AND action_type_k=1";
                }else{
                    $where = "action_type_k=1";
                }
            }
            $source_type_q = 1;//��Դ֧��-ȫ�������Ż� 0 ��֧�� 1֧��
            if($source_type_q==1){
                if($where){
                    $where = $where." AND source_type_q=1";
                }else{
                    $where = "source_type_q=1";
                }
            }
            $Bhdelivery = Helper::M('Bhdelivery');
            $size_info = $Bhdelivery->getAdxs($where);
           if($size_info){
               foreach($size_info as $key1=>$val1){
                   if(!in_array($val1['ad_size_name'],$list_arr)){
                       $list_arr[] =   $val1['ad_size_name'];
                       $lists_arr[] = $val1;
                   }
               }
           }

        }
        $arr = array('api_code' => 1, 'msg' => 'success');
        $arr['data'] = $lists_arr;
        Helper::outJson($arr);
    }
    /*
     * ��ȡȫ�������Ż���Ӧ��ý���Ӧ�Ĺ��
     */
    public function  getSourcetypeqAction(){

        $where  = "";
        $adx_id = Helper::clean($this->getRequest()->getPost('adx_id'));//ý���б�
        if($adx_id){
            if($where){
                $where = $where." AND $adx_id=".$adx_id;
            }else{
                $where = "$adx_id=".$adx_id;;
            }
        }
        $osType_o = Helper::clean($this->getRequest()->getPost('osType_o'));//�Ƿ�֧�ְ�׿
        if($osType_o==1){
            if($where){
                $where = $where." AND osType_o=1";
            }else{
                $where = "osType_o=1";
            }
        }
        $osType_i = Helper::clean($this->getRequest()->getPost('osType_i'));//�Ƿ�֧��ios
        if($osType_i==1){
            if($where){
                $where = $where." AND osType_i=1";
            }else{
                $where = "osType_i=1";
            }
        }
        $behavior_type_t = Helper::clean($this->getRequest()->getPost('behavior_type_t'));//�Ƿ�֧����ҳ��ת
        if($behavior_type_t==1){
            if($where){
                $where = $where." AND behavior_type_t=1";
            }else{
                $where = "behavior_type_t=1";
            }
        }
        $behavior_type_x = Helper::clean($this->getRequest()->getPost('behavior_type_x'));//�Ƿ�֧������Ӧ��
        if($behavior_type_x==1){
            if($where){
                $where = $where." AND behavior_type_x=1";
            }else{
                $where = "behavior_type_x=1";
            }
        }
        $action_type_h = Helper::clean($this->getRequest()->getPost('action_type_h'));//��ʽ֧��-��� 0 ��֧�� 1֧��
        if($action_type_h==1){
            if($where){
                $where = $where." AND action_type_h=1";
            }else{
                $where = "action_type_h=1";
            }
        }
        // $action_type_s = Helper::clean($this->getRequest()->getPost('action_type_s'));//��ʽ֧��-��Ƶ��Ƭ 0 ��֧�� 1֧��
        //$action_type_y = Helper::clean($this->getRequest()->getPost('action_type_y'));//��ʽ֧��-ԭ����Ϣ�� 0 ��֧�� 1֧��
        $action_type_k = Helper::clean($this->getRequest()->getPost('action_type_k'));//��ʽ֧��-���� 0 ��֧�� 1֧��
        if($action_type_k==1){
            if($where){
                $where = $where." AND action_type_k=1";
            }else{
                $where = "action_type_k=1";
            }
        }
        $source_type_q = 1;//��Դ֧��-ȫ�������Ż� 0 ��֧�� 1֧��
        if($source_type_q==1){
            if($where){
                $where = $where." AND source_type_q=1";
            }else{
                $where = "source_type_q=1";
            }
        }
        $Bhdelivery = Helper::M('Bhdelivery');
        $size_info = $Bhdelivery->getAdxs($where);
        $arr = array('api_code' => 1, 'msg' => 'success');
        $arr['data'] = $size_info;
        Helper::outJson($arr);
     }
    /*
     * ��ȡ��Ӧý���Ӧ��app�б�
     */
    public function getAppAction(){

        $adx_Id = Helper::clean($this->getRequest()->getPost('adx_Id'));//ý���б�
        $Bhdelivery = Helper::M('Bhdelivery');
        $applist = $Bhdelivery->getApp($adx_Id);
        $arr = array('api_code' => 1, 'msg' => 'success');
        $arr['data'] = $applist;
        Helper::outJson($arr);
    }
    /*
     *��ȡapp��Ӧ�Ĺ���б�
     */
    public function getAppSizeAction(){

        $where = "";
        $ap_id = Helper::clean($this->getRequest()->getPost('ap_id'));//app��Ӧid
        if($ap_id){
            if($where){
                $where = $where." AND ap_id=".$ap_id;
            }else{
                $where = "ap_id=".$ap_id;;
            }
        }
        $osType_o = Helper::clean($this->getRequest()->getPost('osType_o'));//�Ƿ�֧�ְ�׿
        if($osType_o==1){
            if($where){
                $where = $where." AND osType_o=1";
            }else{
                $where = "osType_o=1";
            }
        }
        $osType_i = Helper::clean($this->getRequest()->getPost('osType_i'));//�Ƿ�֧��ios
        if($osType_i==1){
            if($where){
                $where = $where." AND osType_i=1";
            }else{
                $where = "osType_i=1";
            }
        }
        $behavior_type_t = Helper::clean($this->getRequest()->getPost('behavior_type_t'));//�Ƿ�֧����ҳ��ת
        if($behavior_type_t==1){
            if($where){
                $where = $where." AND behavior_type_t=1";
            }else{
                $where = "behavior_type_t=1";
            }
        }
        $behavior_type_x = Helper::clean($this->getRequest()->getPost('behavior_type_x'));//�Ƿ�֧������Ӧ��
        if($behavior_type_x==1){
            if($where){
                $where = $where." AND behavior_type_x=1";
            }else{
                $where = "behavior_type_x=1";
            }
        }
        $action_type_h = Helper::clean($this->getRequest()->getPost('action_type_h'));//��ʽ֧��-��� 0 ��֧�� 1֧��
        if($action_type_h==1){
            if($where){
                $where = $where." AND action_type_h=1";
            }else{
                $where = "action_type_h=1";
            }
        }
        // $action_type_s = Helper::clean($this->getRequest()->getPost('action_type_s'));//��ʽ֧��-��Ƶ��Ƭ 0 ��֧�� 1֧��
        //$action_type_y = Helper::clean($this->getRequest()->getPost('action_type_y'));//��ʽ֧��-ԭ����Ϣ�� 0 ��֧�� 1֧��
        $action_type_k = Helper::clean($this->getRequest()->getPost('action_type_k'));//��ʽ֧��-���� 0 ��֧�� 1֧��
        if($action_type_k==1){
            if($where){
                $where = $where." AND action_type_k=1";
            }else{
                $where = "action_type_k=1";
            }
        }
        $source_type_z = 1;//��Դ֧��-ָ��Ӧ��
        if($source_type_z==1){
            if($where){
                $where = $where." AND source_type_z=1";
            }else{
                $where = "source_type_z=1";
            }
        }
        $Bhdelivery = Helper::M('Bhdelivery');
        $result = $Bhdelivery->getAppSize($where);
        $arr = array('api_code' => 1, 'msg' => 'success');
        $arr['data'] = $result;
        Helper::outJson($arr);
    }
    /*
     * ��ȡ��Ӧapp���Ϸ��ص��б�
     */
    public function getAppSizesAction(){
        $app_ids = Helper::clean($this->getRequest()->getPost('app_ids'));//ý���б�
        $app_arr = explode(",",$app_ids);
        $list_arr = array();
        $lists_arr = array();
        foreach ($app_arr as $key=>$val) {
            if ($val) {
            $where = "";
            $ap_id = $val;//app��Ӧid
            if ($ap_id) {
                if ($where) {
                    $where = $where . " AND ap_id=" . $ap_id;
                } else {
                    $where = "ap_id=" . $ap_id;;
                }
            }
            $osType_o = Helper::clean($this->getRequest()->getPost('osType_o'));//�Ƿ�֧�ְ�׿
            if ($osType_o == 1) {
                if ($where) {
                    $where = $where . " AND osType_o=1";
                } else {
                    $where = "osType_o=1";
                }
            }
            $osType_i = Helper::clean($this->getRequest()->getPost('osType_i'));//�Ƿ�֧��ios
            if ($osType_i == 1) {
                if ($where) {
                    $where = $where . " AND osType_i=1";
                } else {
                    $where = "osType_i=1";
                }
            }
            $behavior_type_t = Helper::clean($this->getRequest()->getPost('behavior_type_t'));//�Ƿ�֧����ҳ��ת
            if ($behavior_type_t == 1) {
                if ($where) {
                    $where = $where . " AND behavior_type_t=1";
                } else {
                    $where = "behavior_type_t=1";
                }
            }
            $behavior_type_x = Helper::clean($this->getRequest()->getPost('behavior_type_x'));//�Ƿ�֧������Ӧ��
            if ($behavior_type_x == 1) {
                if ($where) {
                    $where = $where . " AND behavior_type_x=1";
                } else {
                    $where = "behavior_type_x=1";
                }
            }
            $action_type_h = Helper::clean($this->getRequest()->getPost('action_type_h'));//��ʽ֧��-��� 0 ��֧�� 1֧��
            if ($action_type_h == 1) {
                if ($where) {
                    $where = $where . " AND action_type_h=1";
                } else {
                    $where = "action_type_h=1";
                }
            }
            // $action_type_s = Helper::clean($this->getRequest()->getPost('action_type_s'));//��ʽ֧��-��Ƶ��Ƭ 0 ��֧�� 1֧��
            //$action_type_y = Helper::clean($this->getRequest()->getPost('action_type_y'));//��ʽ֧��-ԭ����Ϣ�� 0 ��֧�� 1֧��
            $action_type_k = Helper::clean($this->getRequest()->getPost('action_type_k'));//��ʽ֧��-���� 0 ��֧�� 1֧��
            if ($action_type_k == 1) {
                if ($where) {
                    $where = $where . " AND action_type_k=1";
                } else {
                    $where = "action_type_k=1";
                }
            }
            $source_type_z = 1;//��Դ֧��-ָ��Ӧ��
            if ($source_type_z == 1) {
                if ($where) {
                    $where = $where . " AND source_type_z=1";
                } else {
                    $where = "source_type_z=1";
                }
            }
            $Bhdelivery = Helper::M('Bhdelivery');
            $size_info = $Bhdelivery->getAppSize($where);
            if ($size_info) {
                foreach ($size_info as $key1 => $val1) {
                    if (!in_array($val1['ad_size_name'], $list_arr)) {
                        $list_arr[] = $val1['ad_size_name'];
                        $lists_arr[] = $val1;
                    }
                }
            }
        }

        }
        $arr = array('api_code' => 1, 'msg' => 'success');
        $arr['data'] = $lists_arr;
        Helper::outJson($arr);
        }

    /*
     * ��ȡ��Ӧ���Ĵ���
     */
    public function  getCreativeAction(){
        $ad_size_name = Helper::clean($this->getRequest()->getPost('ad_size_name'));//�ϴ����id
        $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//�����id
        $type = Helper::clean($this->getRequest()->getPost('type'));//type
        $data['ad_size_name'] = $ad_size_name;
        $data['comp_id'] = $comp_id;
        $data['type'] = $type;
        $Bhdelivery = Helper::M('Bhdelivery');
        $comp_pid = $this->uinfo['comp_id'];
        $size_info = $Bhdelivery->getSize($ad_size_name);
        if($size_info){
            $access_token = $this->getaccess();
            $url = $this->config['dsp']['url']."material/serach?access_token=".$access_token."&comp_id=".$comp_pid."&comp_pid=".$comp_pid."&group_id=".$size_info[0]['adgroup_id']."&size_id=".$size_info[0]['ad_size_id']."&tag_id=&material_name=&time=&platform_id=";
            $doNet = new DoNetwork();
            $result =json_decode($doNet->makeRequest($url),true);
            $arr = array('api_code' => 1, 'msg' => 'success');
            $arr['data'] =$result['data'];

        }else{
            $arr = array('api_code' => 1, 'msg' => 'success');
            $arr['data'] = array();
        }
        Helper::outJson($arr);
    }
    /*
     * �޸Ĺ�浥Ԫ״̬
     *
     */
    public function updateAdStatusAction(){
        $bh_delivery_id = Helper::clean($this->getRequest()->getPost('bh_delivery_id'));//���id
        $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//comp_id
        $status = Helper::clean($this->getRequest()->getPost('status'));//����޸�״̬getAdinfo
        $Bhdelivery = Helper::M('Bhdelivery');
        $adsInfo = $Bhdelivery->getAdinfo($bh_delivery_id,$comp_id);
        if($adsInfo[0]['status']==3){
            $arr = array('api_code' => 0, 'msg' => '�ù���ѽ���');
        }else{
            $result = $Bhdelivery->updateAdStatus($bh_delivery_id,$status);
            if($result){
                $arr = array('api_code' => 1, 'msg' => '�޸ĳɹ�');
                }else{
                $arr = array('api_code' =>0, 'msg' => '�޸�ʧ�ܣ����Ժ�����');
            }
        }
        Helper::outJson($arr);
        }
    /*
     * �޸ļƻ�״̬
     */
    public function updateCampStatusAction(){
        $bh_campaign_id = Helper::clean($this->getRequest()->getPost('bh_campaign_id'));//�ƻ�id
        $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//comp_id
        $status = (int)Helper::clean($this->getRequest()->getPost('status'));//�ƻ�״̬�޸�
        if($status==2){
            $status_ad = 4;
        }elseif($status==4){
            $status_ad = 2;
        }else{
            $status_ad = 1;
        }
        $this->db    = Yaf_Registry::get('db');
        $this->db->bh->startTrans();
        $Bhdelivery = Helper::M('Bhdelivery');
        if($status==2 || $status==4 || $status==3){
            $result =  $Bhdelivery->updateCampStatus($bh_campaign_id,$status,$comp_id);
            if($result){
                $result_ad =  $Bhdelivery->updateAdsStatus($bh_campaign_id,$status_ad,$status,$comp_id);
                if($result_ad>=0){
                    $this->db->bh->commit();
                    $arr = array('api_code' => 1, 'msg' => 'success');
                }else{
                    $this->db->bh->rollback();
                    $arr = array('api_code' =>0, 'msg' => '�޸�ʧ�ܣ����Ժ�����');
                }
            }else{
                $arr = array('api_code' =>0, 'msg' => '�޸�ʧ�ܣ����Ժ�����');
            }

        }else{
            $arr = array('api_code' =>0, 'msg' => '״ֻ̬���л�Ϊ��ͣ��Ͷ����');
        }
        Helper::outJson($arr);

   }
   /*
    * �޸ļƻ�����
    */
   public function updateCampnameAction(){

       $bh_campaign_name = Helper::clean($this->getRequest()->getPost('bh_campaign_name'));//���ƻ�����
       $bh_campaign_id =  Helper::clean($this->getRequest()->getPost('bh_campaign_id'));//���ƻ�����
       $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//�����id
       $Bhdelivery = Helper::M('Bhdelivery');
       $result = $Bhdelivery->checkCam($comp_id,$bh_campaign_name);
       if($result){
           $arr = array('api_code' => 0, 'msg' => '�üƻ������Ѿ���ʹ��');

       }else{
           $where['bh_campaign_id'] = $bh_campaign_id;
           $where['comp_id'] = $comp_id;
          $data['bh_campaign_name'] =  $bh_campaign_name;
          $result_update = $Bhdelivery->updateCampname($where,$data);
          if($result_update){
              $arr = array('api_code' => 1, 'msg' => '�޸ĳɹ�');
          }else{
              $arr = array('api_code' => 0, 'msg' => '�޸�ʧ��');
          }

       }
       Helper::outJson($arr);

   }
    /*
     * ��������
     */
    public function addCreativeAction(){

        $ad_size_name = Helper::clean($this->getRequest()->getPost('ad_size_name'));//�ϴ����id
        $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//�����id
        $creative_name = Helper::clean($this->getRequest()->getPost('creative_name'));//��������
        $jump_url = Helper::clean($this->getRequest()->getPost('jump_url'));//��ת��ַ
        $img_url = Helper::clean($this->getRequest()->getPost('img_url'));//ͼƬurl
        $type  = Helper::clean($this->getRequest()->getPost('type'));//��Դ  1 ȫ�������Ż� 2  ָ��Ӧ��
        $data['ad_size_name'] = $ad_size_name;
        $data['creative_name'] = $creative_name;
        $data['jump_url'] = $jump_url;
        $data['img_url'] = $img_url;
        $data['type'] = $type;
        $data['comp_id'] = $comp_id;
        $data['comp_pid'] =  $this->uinfo['comp_pid'];
        $data['add_time'] = time();
        $Bhdelivery = Helper::M('Bhdelivery');
        $result = $Bhdelivery->addCreative($data);
        $arr = array('api_code' => 1, 'msg' => 'success');
        $arr['data'] = $result;
        Helper::outJson($arr);

    }
    /*
     * ��ȡȫ�����ʡ���б�
     */
    public function getBhCityAction(){
        $Bh_city_dao = Helper::M("BhCity");
        $pid = Helper::clean($this->getRequest()->getPost('pid'));//���и���ID
        if($pid==0 || $pid==''){
        	$level=1;
        }else{
        	$level=2;
        }
        $cityList = $Bh_city_dao->getBhCity($pid,$level);
        Helper::outJson(array('api_code'=>1,'msg'=>'','data'=>$cityList));
    }
	/*
     * ��ȡȫ����� �µ�����Ȧ�б�
     */
    public function getBhCityAreaAction(){
        $Bh_city_dao = Helper::M("BhCity");
        $type = Helper::clean($this->getRequest()->getPost('type'));//��ȡ����
        $cityId = Helper::clean($this->getRequest()->getPost('cityId'));//��ȡ����
        $areaList = $Bh_city_dao->getBhCityArea($type,$cityId);
        Helper::outJson(array('api_code'=>1,'msg'=>'','data'=>$areaList));
    }
    /*
     *��ȡ
     */

    /*
    * �������
    */
    public function addDeliveryAction(){

        $bh_campaign_id = Helper::clean($this->getRequest()->getPost('bh_campaign_id'));//���ƻ�id
        $type = Helper::clean($this->getRequest()->getPost('type'));//Ͷ��ƽ̨ 1 PC��  2 �ƶ���
        $osType = Helper::clean($this->getRequest()->getPost('osType'));//Ͷ������ 1 Android  2 iOS
        $budgetControlType = Helper::clean($this->getRequest()->getPost('budgetControlType'));//Ͷ���������� 0 �����ѿ���  1 ��չʾ������
        $budgetAllocationType = Helper::clean($this->getRequest()->getPost('budgetAllocationType'));//�����ѿ������� 1���� 2 ����
        $budgetAllocationMoney = Helper::clean($this->getRequest()->getPost('budgetAllocationMoney'));//�����ѿ��Ƶ���
        $budgetAllocationCycle = Helper::clean($this->getRequest()->getPost('budgetAllocationCycle'));//�����ѿ��Ƶ�λ��1 Сʱ 2 �죩
        $budgetControlVal = Helper::clean($this->getRequest()->getPost('budgetControlVal'));//��չʾ������ ���� ��ǧ�ε�λ��
        $budgetAllocationDisplay = Helper::clean($this->getRequest()->getPost('budgetAllocationDisplay'));//��չʾ�����Ƶ�λ��1 Сʱ 2 �죩
        $frequencyNum = Helper::clean($this->getRequest()->getPost('frequencyNum'));//Ƶ�ο��� ����
        $frequencyCycle = Helper::clean($this->getRequest()->getPost('frequencyCycle'));//Ƶ�ο��Ƶ�λ 1Сʱ 2�� 3�� 4��
        $beheGender = Helper::clean($this->getRequest()->getPost('beheGender'));//�Ա����
        $beheAge = Helper::clean($this->getRequest()->getPost('beheAge'));//�������
        $customIp = Helper::clean($this->getRequest()->getPost('customIp'));//ip����Χ
        $areaType =  Helper::clean($this->getRequest()->getPost('areaType'));//Ͷ���������� 1 ʡ�� 4 ��Ȧ
        $lbs = Helper::clean($this->getRequest()->getPost('lbs'));//���򼯺�
        $behaviorType = Helper::clean($this->getRequest()->getPost('behaviorType'));//������Ϊ1��תҳ��2����Ӧ��3΢��Ʒ��ҳ
        $creativeType = Helper::clean($this->getRequest()->getPost('creativeType'));//������ʽ 1��� 2��Ƶ��Ƭ 3 ���ܴ��� 4JS�ز� 5 ����6 ԭ����Ϣ�� 7 ������
        $mediaType = Helper::clean($this->getRequest()->getPost('mediaType'));//ý��ѡ�� 1ȫ�������Ż� 2ָ��Ӧ�� 3ָ�����λ 4ָ��Ӧ������ 5ָ��ý�����
        $adxType = Helper::clean($this->getRequest()->getPost('adxType'));//ý�弯��
        $appIn = Helper::clean($this->getRequest()->getPost('appIn'));//Ӧ�ü���
        $advertData = Helper::clean($this->getRequest()->getPost('advertData'));//���⼯��
        $customCrowdIn = Helper::clean($this->getRequest()->getPost('customCrowdIn'));//��Ⱥ���������
        $delivery_name = Helper::clean($this->getRequest()->getPost('delivery_name'));//���Ͷ������
        $startDate = Helper::clean($this->getRequest()->getPost('startDate'));//Ͷ�ſ�ʼ����
        $endDate = Helper::clean($this->getRequest()->getPost('endDate'));//Ͷ�Ž�������
        $dateTimeInfo = Helper::clean($this->getRequest()->getPost('dateTimeInfo'));//�Զ���ʱ�伯��
        $holidayType = Helper::clean($this->getRequest()->getPost('holidayType'));//�ڼ�������
        $bidTyp = Helper::clean($this->getRequest()->getPost('bidTyp'));//Ͷ�����ͣ�1 CPM 1CPC��
        $bid = Helper::clean($this->getRequest()->getPost('bid'));//����
        $comp_pid = $this->uinfo['comp_id'];//������id
        $comp_id = Helper::clean($this->getRequest()->getPost('comp_id'));//�����id
        $add_staff_id = $this->uinfo['staff_id'];;
        $add_time = time();

        $data['bh_campaign_id'] = $bh_campaign_id;
        $data['type'] = $type;
        $data['osType'] = $osType;
        $data['budgetControlType'] = $budgetControlType;
        $data['budgetAllocationType'] = $budgetAllocationType;
        $data['budgetAllocationMoney'] = $budgetAllocationMoney*100;
        $data['budgetAllocationCycle'] = $budgetAllocationCycle;
        $data['budgetControlVal'] = $budgetControlVal;
        $data['budgetAllocationDisplay'] = $budgetAllocationDisplay;
        $data['frequencyNum'] = $frequencyNum;
        $data['frequencyCycle'] = $frequencyCycle;
        $data['beheGender'] = $beheGender;
        $data['beheAge'] = $beheAge;
        $data['areaType'] = $areaType;
        $data['lbs'] = $lbs;
        $data['customIp'] = $customIp;
        $data['behaviorType'] = $behaviorType;
        $data['creativeType'] = $creativeType;
        $data['mediaType'] = $mediaType;
        $data['adxType'] = $adxType;
        $data['appIn'] = $appIn;
        $data['advertData'] = $advertData;
        $data['customCrowdIn'] = $customCrowdIn;
        $data['delivery_name'] = $delivery_name;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['dateTimeInfo'] = $dateTimeInfo;
        $data['holidayType'] = $holidayType;
        $data['bidTyp'] = $bidTyp;
        $data['bid'] = $bid*100;
        $data['comp_id'] = $comp_id;
        $data['comp_pid'] = $comp_pid;
        $data['add_staff_id'] = $add_staff_id;
        $data['add_time'] = $add_time;
        $Bhdelivery = Helper::M('Bhdelivery');
        $check_name = $Bhdelivery->checkAdname($comp_id,$delivery_name);
        if($check_name){
            $arr = array('api_code' => 0, 'msg' => '�����ѱ�ʹ��');
            Helper::outJson($arr);
        }
        $result = $Bhdelivery->addDelivery($data);
        $arr = array('api_code' => 1, 'msg' => 'success');
        $arr['data'] = $result;
        Helper::outJson($arr);
    }
    /*
      * ��ȡָ��Ӧ�ö�Ӧ���й���ý���б�
      */
    public function getZadxsAction(){
        $where  = "";
        $osType_o = Helper::clean($this->getRequest()->getPost('osType_o'));//�Ƿ�֧�ְ�׿
        if($osType_o==1){
            if($where){
                $where = $where." AND osType_o=1";
            }else{
                $where = "osType_o=1";

            }
        }
        $osType_i = Helper::clean($this->getRequest()->getPost('osType_i'));//�Ƿ�֧��ios
        if($osType_i==1){
            if($where){
                $where = $where." AND osType_i=1";
            }else{
                $where = "osType_i=1";
            }
        }
        $behavior_type_t = Helper::clean($this->getRequest()->getPost('behavior_type_t'));//�Ƿ�֧����ҳ��ת
        if($behavior_type_t==1){
            if($where){
                $where = $where." AND behavior_type_t=1";
            }else{
                $where = "behavior_type_t=1";
            }
        }
        $behavior_type_x = Helper::clean($this->getRequest()->getPost('behavior_type_x'));//�Ƿ�֧������Ӧ��
        if($behavior_type_x==1){
            if($where){
                $where = $where." AND behavior_type_x=1";
            }else{
                $where = "behavior_type_x=1";
            }
        }
        $action_type_h = Helper::clean($this->getRequest()->getPost('action_type_h'));//��ʽ֧��-��� 0 ��֧�� 1֧��
        if($action_type_h==1){
            if($where){
                $where = $where." AND action_type_h=1";
            }else{
                $where = "action_type_h=1";
            }
        }
        // $action_type_s = Helper::clean($this->getRequest()->getPost('action_type_s'));//��ʽ֧��-��Ƶ��Ƭ 0 ��֧�� 1֧��
        //$action_type_y = Helper::clean($this->getRequest()->getPost('action_type_y'));//��ʽ֧��-ԭ����Ϣ�� 0 ��֧�� 1֧��
        $action_type_k = Helper::clean($this->getRequest()->getPost('action_type_k'));
        if($action_type_k==1){
            if($where){
                $where = $where." AND action_type_k=1";
            }else{
                $where = "action_type_k=1";
            }
        }
        $Bhdelivery = Helper::M('Bhdelivery');
        $size_info = $Bhdelivery->appSizelists($where);
        $app_list = array();
        if($size_info){
            /*
             * ��ȡapp�б�
             */
            foreach($size_info as $key=>$val){
                if(!in_array($val['ap_id'],$app_list)){
                    $app_list[] = $val['ap_id'];
                }
            }
            /*
               * ��ȡ��Ӧ��ý���б�
               */
            $adx_list_arr = array();
            $adx_list = '(';
            foreach($app_list as $key1=>$val){
                $app_info =  $Bhdelivery->getAppinfo($val);
                if(!in_array($app_info[0]['adx_Id'],$adx_list_arr)){
                    $adx_list_arr[] = $app_info[0]['adx_Id'];
                    if($adx_list=='('){
                        $adx_list = $adx_list.$app_info[0]['adx_Id'];
                    }else{
                        $adx_list = $adx_list.",".$app_info[0]['adx_Id'];
                    }

                }
            }
            $adx_list = $adx_list.")";
            $adx_info = $Bhdelivery->getAdxsinfo($adx_list);
            foreach ($adx_info as $key2=>$val2){
                foreach($app_list as $key3=>$val3){
                    $app_info =  $Bhdelivery->getAppinfo($val3);
                    if($app_info[0]['adx_Id']==$val2['adx_Id']){
                        $adx_info[$key2]['app_info'][] = $app_info[0];
                    }

                }
            }
            $arr = array('api_code' => 1, 'msg' => 'success');
            $arr['data'] = $adx_info;
            Helper::outJson($arr);
        }else{
            $arr = array('api_code' => 1, 'msg' => 'success');
            $arr['data'] = '';
            Helper::outJson($arr);
        }



    }
}
