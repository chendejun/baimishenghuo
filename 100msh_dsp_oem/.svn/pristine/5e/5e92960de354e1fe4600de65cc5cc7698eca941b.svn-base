<?php

/**
 *
 */
class TestController extends BasicController
{

    public function indexAction()
    {
$account_id = 1697020333;
$data = [
    'randomNumber'=> '87bed061433798092162bafd6185a714',
    'creativeType'=>['9'],
    'adxType'=>9,
    'domainIn'=> '',
    'adPlaceIn'=> '',
    'appCategory'=> '',
    'whitelistIn'=> '',
    'eType'=> '',
    'v'=> '07844430075019806'
    ];


        $rel = BhDspApi::getEstimateFlow($account_id,$data);

Helper::outJson(json_decode($rel,true));

    }


    public function getBhTimeStatisticsAction()
    {
        $account_id = 1697020333;
//        $data = [
//            'cityId'=> 110000
//        ];
		 $data['randomNumber'] = 'a1e1f5eac39f5258aae8b7e7c3dc1661';
		 $data['creativeType'] = 1;//创意类型
		 $data['adxType'] = '';//媒体类型
		 $data['domainIn'] = '';//系统
		 $data['adPlaceIn'] = '';
		 $data['appCategory'] = '';
		 $data['whitelistIn'] = '';
		 $data['eType'] = 'size';
		 $data['v'] = '0958105891494268';

        $rel = BhDspApi::getEstimateFlow($account_id,$data);
        Helper::outJson(json_decode($rel,true));
    }
}
