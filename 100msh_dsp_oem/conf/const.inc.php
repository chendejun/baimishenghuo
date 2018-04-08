<?php
const LIB_PATH = APP_PATH.'/application/library';
const CORE_PATH = LIB_PATH.'/core';
const LOG_PATH = APP_PATH.'/application/log/';

//角色
const ROLE_ROOT = 1; //超级管理员
const ROLE_FINANCE = 2; //财务人员
const ROLE_OPERATED = 3; //投放人员
const ROLE_MANAGER = 4; //普通管理员

//账户类型（0-无(虚拟)，1-Ⅱ类账户(现金)，2-Ⅰ类账户(赠送)）
const ACCOUNT_GIFT = 0; //虚拟账户
const ACCOUNT_CASH = 1; //Ⅱ类账户[现金账户]
const ACCOUNT_GIVE = 2; //Ⅰ类账户[赠送账户]

//投放平台
const DSP_BMSH  = 1; //百米生活DSP平台
const DSP_TXGDT = 2; //腾讯广点通DSP平台
const DSP_WXXF  = 3; //微信吸粉
const DSP_SMS   = 4; //短信发送营销
const DSP_BEHE  = 5; //behe广告