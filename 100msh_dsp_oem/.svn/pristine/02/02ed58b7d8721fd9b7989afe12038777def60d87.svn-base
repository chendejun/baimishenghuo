<?php
class SettleController extends BasicController {
	public function indexAction(){
        $agent_level=$this->uinfo['agent_level'];
        if($agent_level >= 3){
            $if_opear=0;
        }else{
            $if_opear=1;
        }
        //var_dump($agent_level);
        $this->getView()->assign('if_opear' , $if_opear);
	}
	//同步行业分类
	function industryListAction(){
        $res=json_decode(BhDspApi::industryList(),true);
        //var_dump($res);
        $adv=Helper::M('Advertiser');
        $sql="INSERT INTO dsp_comp_category1 (`cate_id`,`cate_pid`,`cate_name`) VALUES ";
        foreach ($res['datas'] as $k=>$v){
            $sql.="({$v['id']},0,'{$v['name']}'),";
            $data=array('Id'=>$v['id'],'v'=>mt_rand(10000000000000000,99999999999999999));
            $list=json_decode(BhDspApi::industryCdList($data),true);
            foreach ($list['datas'] as $k1=>$v1){
                $sql.="({$v1['id']},{$v['id']},'{$v1['name']}'),";
            }
        }
        $sql=trim($sql,',');
        echo $sql;exit;
        $id=$adv->insetCate($sql);
        if($id){
            echo "111";
        }else{
            echo "0";
        }
        exit;
    }
    function addAdvAction(){
        $data=array();
        $data['userName']='18927893502@100msh.com';
        $data['realName']='房朝俭';
        $data['mobile']='18927893502';
        $data['accountType']=2;
        $data['companyCateId']='101200';
        $data['companySubCateId']='101210';
        $data['passWord']='18927893502';
        $data['rePassword']='18927893502';
        $data['groupId']=3778;
        $data['companyName']='螺蒸鸡食府';
        $data['accountAptitude']='';
        $data['companyUrl']='http://www.meituan.com/';
        $data['companyAddress']='广东省韶关市浈江区南郊三公里沙梨园村169号临街一楼';
        $data['v']=mt_rand(10000000000000000,99999999999999999);
        $res=json_decode(BhDspApi::addAccount($data),true);
        var_dump($res);

    }
    function qualificationsAction(){
        libxml_use_internal_errors(true);
        include(LIB_PATH . '/phpQuery/phpQuery.php');
        $html=BhDspApi::qualifications('1697020333');
        phpQuery::newDocumentHTML($html);
        //1.0已通过
        //echo "1.已通过:<br/>";
        $lh=pq(".bh_pass_area:eq(1) .special")->length;
        if($lh > 0){
            $list['pass']=pq(".bh_pass_area:eq(1) .special");
            foreach ($list['pass'] as $v){
                preg_match("/<p class=\"specialMeg\">(.*?)<\/p>/",pq($v)->html(),$a);
                $aaa=preg_replace("/<p class=\"specialMeg\">.*?<\/p>/",'',pq($v)->html());
                if(!empty($a)){
                    echo trim($aaa)."+++".trim($a[1])."<br/>";

                }else{
                    echo trim($aaa)."+++<br/>";
                }

            }
        }else{
            echo "++++++没有++++++<br/>";
        }
        //2.0未通过
        echo "2.未通过:<br/>";
        $lh=pq(".bh_refuse_area:eq(1) .special")->length;
        if($lh > 0){
            $list['unpass']=pq(".bh_refuse_area:eq(1) .special");
            foreach ($list['unpass'] as $v){
                preg_match("/<p class=\"specialMeg\">(.*?)<\/p>/",pq($v)->html(),$a);
                //var_dump($a);
                $aaa=preg_replace("/<p class=\"specialMeg\">.*?<\/p>/",'',pq($v)->html());
                if(!empty($a)){
                    echo trim($aaa)."+++".trim($a[1])."<br/>";
                }else{
                    echo trim($aaa)."+++<br/>";
                }
            }
        }else{
            echo "++++++没有++++++<br/>";
        }
        //3.0
        echo "3.待审核:<br/>";
        $lh=pq(".bh_wait_area:eq(1) .special")->length;
        if($lh > 0){
            $list['wait']=pq(".bh_wait_area:eq(1) .special");
            foreach ($list['wait'] as $v){
                preg_match("/<p class=\"specialMeg\">(.*?)<\/p>/",pq($v)->html(),$a);
                // var_dump($a);
                $aaa=preg_replace("/<p class=\"specialMeg\">.*?<\/p>/",'',pq($v)->html());
                if(!empty($a)){
                    echo trim($aaa)."+++".trim($a[1])."<br/>";
                }else{
                    echo trim($aaa)."+++<br/>";
                }
            }
        }else{
            echo "++++++没有++++++<br/>";
        }

        exit;
    }
    function upImgAction(){
        $data['files[]']=new curlFile("/data001/data/sites/100msh_upload/dsp/adv/f9a0e311c9909cc2ec37916f39f05d9d.jpg");
        $res=json_decode(BhDspApi::upload($data),true);
        var_dump($res);exit;
    }
    function premiumAction(){
        $data=array();
        $data['premium']=60;
        $data['premiumType']='account';
        $data['accountId']='1697021015';
        $res=BhDspApi::premium($data);
        var_dump($res);
    }
    function addMenuAction(){
        WorkWeixin::addMenu();
    }
    function delMenuAction(){
        WorkWeixin::delMenu();
    }
}