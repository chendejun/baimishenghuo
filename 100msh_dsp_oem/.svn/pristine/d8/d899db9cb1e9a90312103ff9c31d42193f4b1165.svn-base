<?php

class OfficialAccounts extends Model
{
    /**
     * [is_whitelist 判断公众号白名单]
     * @param  [int]  $pn_id [公众号id]
     * @return boolean        [description]
     */
    public function is_whitelist($pn_id)
    {
        $rel = $this->db->dsp->table('t_wechat_public_number')->where('pn_id = '.$pn_id.' and is_whitelist = 1 ')->find();

        if (count($rel)>0) {
            return true;
        }

        return false;
    }

    /**
     * [addOfficialAccounts 设置公众号信息]
     * @param [array] $data [创建公众号所需数据]
     */
    protected function setOfficialAccountInfo($data)
    {
        $official_account_id = $this->db->dsp->table('t_wechat_public_number')->data($data)->insert();

        return $official_account_id;
    }

    /**
     * [addShopInfo 设置门店信息]
     * @param [array] $data [门店信息]
     */
    protected function setShopInfo($data)
    {
        $shop_id = $this->db->dsp->table('t_wechat_shop_info')->data($data)->insert();

        return $shop_id;

    }

    /**
     * [addOfficialAccountInfo 绑定公众号]
     * @param [type] $official_account_data [公众号信息]
     * @param [type] $shop_data             [门店信息]
     */
    public function addOfficialAccount($official_account_data, $shop_data)
    {
        $this->db->dsp->startTrans();
        $official_accounts_id = $this->setOfficialAccountInfo($official_account_data);
        if ($official_accounts_id) {
            // 插入门店信息
            $shop_id = $this->setShopInfo($shop_data);
        }
        if ($official_accounts_id && $shop_id) {
            $this->db->dsp->commit();
            return true;
        }else{
            $this->db->dsp->rollback();
            return false;
        }
    }

    /**
     * [selectOfficialAccounts 查找公众号是否存在]
     * @param  [type] $column_where [该公众号唯一列条件]
     * @return [int]         [成功返回公众号id]
     */
    public function selectOfficialAccounts($column_where)
    {
        $id = $this->db->dsp->table('t_wechat_public_number')->where($column_where)->find();

        return !empty($id) ? $id : 0 ;

    }

    /**
     * [selectAllOfficialAccount 查找所有公众号]
     * @param  [int] $page  [当前页数]
     * @param  [int] $limit [每页显示条目数]
     * @return [array]        [结果集]
     */
    public function selectAllOfficialAccount($page,$limit)
    {

        $uinfo = $_SESSION['uinfo'];
        $field = "a.pn_id ,
        a.pn_add_uid ,
        a.pn_name ,
        FROM_UNIXTIME(a.pn_add_time) as create_at ,
        ifnull(b.plan_name,'无') as plan_name ,
        ifnull(sum(b.new_add_followers),0) as accumulative_followers ,
        ifnull(sum(b.freezing_amount),0) as freezing_amount ,
        ifnull(sum(b.consumption_amount),0) as accumulative_consumption ";
        $where = " where a.pn_add_uid = ".$uinfo['staff_id'];
        $sql = " select ".$field.
        " FROM t_wechat_public_number as a left join t_plans as b on a.pn_id = b.pn_id ".$where."  group by a.pn_id order by a.pn_add_time desc  limit ".$page.','.$limit;
        $rel = $this->db->dsp->query($sql);

        return $rel;
    }


    /**
     * [officialAccountList 查找所有公众号用于公众号选择]
     * @return [type] [description]
     */
    public function officialAccountList()
    {
        $uinfo = $_SESSION['uinfo'];
        $where = ' pn_add_uid= '.$uinfo['staff_id'];
        $rel = $this->db->dsp->table('t_wechat_public_number')->field('pn_id ,pn_name')->where($where)->select();
        return $rel;

    }

    /**
     * [officialAccountList 查找所有公众号数量]
     * @return [type] [description]
     */
    public function officialAccountCount()
    {
        $uinfo = $_SESSION['uinfo'];
        $where = ' pn_add_uid= '.$uinfo['staff_id'];
        $num = $this->db->dsp->table('t_wechat_public_number')->field('pn_id ,pn_name')->where($where)->count();
        return $num;

    }

    /**
     * [getPlanId 得到公众号计划id，用于判断公众号时候投递计划]
     * @return [type] [description]
     */
    public function getPlanId($pn_id)
    {
        $sql = 'select b.plan_id from t_wechat_public_number as a inner join t_plans as b on a.pn_id = b.pn_id
        where a.pn_id='.$pn_id;
        $rel = $this->db->dsp->query($sql);
        return $rel;

    }

    public function del($pn_id)
    {
        $id = $this->db->dsp->table('t_wechat_public_number')->where('pn_id='.$pn_id)->delete();

        return $id;

    }

    /**
     * [getInfo 得到公众号详情]
     * @return [type] [description]
     */
    public function getInfo($pn_id)
    {


        $field = "a.pn_name,
        a.public_type,
        a.pn_number,
        b.shop_name,
        a.pn_appid,
        b.shop_id,
        b.ssid,
        a.pn_appsecret,
        b.secret_key,
        a.mobile_decrypt_key,
        a.biz_code,
        a.public_cate,
        a.pn_logo,
        a.pn_qrcode";
        $sql = 'select '.$field.' from t_wechat_public_number as a left join t_wechat_shop_info as b
        on a.pn_appid = b.app_id where a.pn_id='.$pn_id;

        $rel = $this->db->dsp->query($sql);

        return $rel[0];

    }

}
