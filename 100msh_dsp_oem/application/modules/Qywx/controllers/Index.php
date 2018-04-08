<?php
class IndexController extends BasicController {
	public function indexAction(){
		Yaf_Dispatcher::getInstance()->disableView();
		echo 'Qywx-Index-index';
	}
}