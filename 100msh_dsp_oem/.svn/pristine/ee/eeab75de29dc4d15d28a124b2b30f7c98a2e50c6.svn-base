<?php
class LoginController extends BasicController {
	/**
	 * 登录
	 * @return [type] [description]
	 */
	public function indexAction(){
		if($this->getRequest()->isPost()){
			Yaf_Dispatcher::getInstance()->disableView();
			$user_name = Helper::clean($this->getRequest()->getPost('user_name'));
			$pwd = Helper::clean($this->getRequest()->getPost('pwd'));
			$code = Helper::clean($this->getRequest()->getPost('code'));
			if(empty($_SESSION) || strtolower($code) != $_SESSION['captcha_code']){
				echo json_encode(array('state' => 0 , 'msg'=>'验证码错误！')); exit;
			}
			if(empty($user_name) || empty($pwd)){
				echo json_encode(array('state' => 0 , 'msg'=>'用户名或密码不能为空！')); exit;
			}
			$m = Helper::M('U');
			$data = $m->login($user_name , $pwd);
			//var_dump($data);
			if(empty($data)){
				echo json_encode(array('state' => 0 , 'msg'=>'用户名跟密码不匹配！')); exit;
			}else if($data == '-1'){
                echo json_encode(array('state' => 0 , 'msg'=>'你的账号已被停用，如有需要，请联系你的运营人员了解详情。')); exit;
            }else{
                echo json_encode(array('state' => 1 , 'jump'=>'/')); exit;
            }

		}
		setcookie("curmenu", '[]' , time()-10086);
		setcookie("str_menu", '[]' , time()-10086);
		$m = Helper::M('Nav');
		$css = array(1 => 'default' , 2 => 'bkgreen' , 3 => 'darkblue' , 4 => 'mountain' , 5 => 'starsky');
        $default_skin = array('site_color' => 'default' , 'site_logo' => '/public/img/logo1.png' , 'site_bottom_name' => 'Copyright     2018 深圳市百米生活股份有限公司' , 'site_name' => '晓客营销系统');
		$skin = $m->getSkin(0 , Helper::getHost());
		if(empty($skin)){
			$skin = $default_skin;
		}else{
			$skin['site_color'] = $css[$skin['site_color']];
		}
		$this->getView()->assign('skin' , $skin);
	}
	/**
	 * 二维码
	 * @return [type] [description]
	 */
	public function captchaAction(){
		Yaf_Dispatcher::getInstance()->disableView();
        $captcha = new Captcha();
        echo $captcha->doimg();
        $_SESSION['captcha_code'] = $captcha->getCode();//验证码保存到SESSION中
    }
    public function passcodeAction(){
    	Yaf_Dispatcher::getInstance()->disableView();
        $captcha = new Captcha();
        echo $captcha->doimg();
        $_SESSION['pass_code'] = $captcha->getCode();//验证码保存到SESSION中
    }
    /**
     * 找回密码
     * @return [type] [description]
     */
    public function passportAction(){
    	if($this->getRequest()->isPost()){
    		$code = Helper::clean($this->getRequest()->getPost('code'));
			if(strtolower($code) != $_SESSION['pass_code']){
				echo json_encode(array('state' => 0 , 'msg'=>'验证码错误！')); exit;
			}
			$user_name = Helper::clean($this->getRequest()->getPost('user_name'));
			$m = Helper::M('U');
			$result = $m->get($user_name);
			if(empty($result)){
				Helper::outJson(array('state' => 0 , 'msg' => '账号不存在,请重新输入！'));
			}else{
				$num = $this->redis->incrBy('pp_'.$user_name);
				if($num == 1){
					$ttl = strtotime(date("Y-m-d",time())) + 24 * 3600;
					$this->redis->expireAt('pp_'.$user_name , $ttl);
				}elseif($num >3 ){
					Helper::outJson(array('state'=>0,'msg'=>'每天最多可找回密码三次！'));
				}
				
				$nav = Helper::M('Nav');
				$comp_name = '百米生活';
				$skin = $nav->getSkin(0 , Helper::getHost());
				if(!empty($skin)){
					$comp_info = $m->compInfo($skin['comp_pid']);
					if(!empty($comp_info)) $comp_name = $comp_info['comp_name'];
				}
				$code = substr(str_shuffle('0123456789'), 0, 4);
				$content = "【{$comp_name}】验证码{$code}，请在3分钟内填写验证码完成校验身份操作！";
				$result = BmDspApi::request('sms/send' , array('content' => $content ,'mobile_phone'=>$user_name)); //发送短信验证码
				if(empty($result) || isset($result['error'])){
					Helper::outJson(array('state'=>0,'msg'=>empty($result)?'短信发送异常，请稍后再试！':$result['error']));
				}else{
					
					$_SESSION['find_user_name'] = $user_name;
					$_SESSION['passport_code'] = $code;
					Helper::outJson(array('state' => 1 , 'msg' => '已向尾号'.substr($user_name, -4).'的手机号发送短信验证码！' , 'comp_name' => $comp_name));
				}
			}
    	}
    	$m = Helper::M('Nav');
    	$css = array(1 => 'default' , 2 => 'bkgreen' , 3 => 'darkblue' , 4 => 'mountain' , 5 => 'starsky');
        $default_skin = array('site_color' => 'default' , 'site_logo' => '/public/img/logo1.png' , 'site_bottom_name' => 'Copyright     2018 深圳市百米生活股份有限公司' , 'site_name' => '晓客营销系统');
		$skin = $m->getSkin(0 , Helper::getHost());
		if(empty($skin)){
			$skin = $default_skin;
		}else{
			$skin['site_color'] = $css[$skin['site_color']];
		}
		// $user_name = '';
		// if(isset($_GET['user_name'])) $user_name = trim($user_name);
		// $this->getView()->assign('user_name' , $user_name);
		$this->getView()->assign('skin' , $skin);
    }
    /**
     * 验证短信验证码
     */
    public function checkcodeAction(){
    	if(!isset($_SESSION['passport_code'])){
    		header("Location: {$this->url}");
    		exit;
    	}
    	if($this->getRequest()->isPost()){
    		$code = Helper::clean($this->getRequest()->getPost('code'));
			if(strtolower($code) != $_SESSION['passport_code']){
				Helper::outJson(array('state' => 0 , 'msg'=>'验证码错误！'));
			}else{
				$_SESSION['find_pwd'] = 1;
				Helper::outJson(array('state' => 1 , 'msg'=>''));
			}
		}
    }
    /**
     * 充值密码
     * @return [type] [description]
     */
    public function editpwdAction(){
    	if(!isset($_SESSION['find_pwd']) || !isset($_SESSION['find_user_name'])){
    		header("Location: {$this->url}"); exit;
    	}
    	if($this->getRequest()->isPost()){
			Yaf_Dispatcher::getInstance()->disableView();
			$pwd = Helper::clean($this->getRequest()->getPost('pwd'));
			if(empty($pwd)){
				Helper::outJson(array('state' => 0 , 'msg' => '密码不能为空！'));
			}
			$m = Helper::M('U');
			$uinfo = $m->get($_SESSION['find_user_name']);
			$result = $m->editPwd($pwd , $uinfo['staff_id']);
			if($result >= 0){
				Helper::outJson(array('state' => 1 , 'msg' => '修改成功！'));
			}else{
				Helper::outJson(array('state' => 0 , 'msg' => '登录异常！'));
			}
		}
    }
}