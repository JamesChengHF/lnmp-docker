<?php
/****
 * 活动数据
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class jx_activeControl extends mobileHomeControl
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function for_shopOp()
    {
        //$active_name           = '四月份活动商城专题';

        $member_info           = $this->getMemberInfo();
        $redtype               = $_POST['redtype'];
        $active_name           = trim($_POST['active_name']);
        $is_redpack            = !empty($_POST['is_redpack']) ? $_POST['is_redpack'] : 0;
        $data                  = array();

        $data['redpack_list'] = [];
        if($is_redpack == 1){
            $redpack_ten_name      = trim($_POST['redpack_ten_name']);
            $redpack_nineteen_name = trim($_POST['redpack_nineteen_name']);
            //十点红包模板
            $redpack_ten_id = $this->getredpack_id($redpack_ten_name);
            //十九点红包模板
            $redpack_nineteen_id = $this->getredpack_id($redpack_nineteen_name);
            //是否显示10点开抢
            if (10 > date('H')) {
                $data['ten_show_status'] = 1;
            }
            if (16 > date('H')) {
                $data['nineteen_show_status'] = 1;
            }

            if (!$redtype) {
                if (16 <= date('H')) {
                    $redtype = 'nine';
                }
            }
            if ($redtype == 'nine') {
                $redpack_id = $redpack_nineteen_id;
            } else {
                $redpack_id = $redpack_ten_id;
            }
            $redpack_list = $this->time_redpack_list($redpack_id, $member_info);
            foreach ($redpack_list as $key => $v) {
                $redpack_list[$key]['rpacket_t_price'] = floatval($v['rpacket_t_price']);
                $redpack_list[$key]['rpacket_t_limit'] = floatval($v['rpacket_t_limit']);
            }
            //用户切换优先
            if ($redtype == 'nine') {
                $data['redpack_list'] = $redpack_list;
                $data['redtype']      = 'nine';
            } else {
                if ($redtype == 'ten') {
                    $data['redpack_list'] = $redpack_list;
                    $data['redtype']      = 'ten';
                } else {
                    //用户未切换已当前时间进行默认
                    if (16 <= date('H')) {
                        $data['redpack_list'] = $redpack_list;
                        $data['redtype']      = 'nine';
                    } elseif (10 <= date('H')) {
                        $data['redpack_list'] = $redpack_list;
                        $data['redtype']      = 'ten';
                    } else {
                        $data['redpack_list'] = $redpack_list;
                    }
                }
            }
        }

        $tag_id    = $this->get_tagid($active_name);
        $field     = "goods.goods_name,goods.goods_image,goods.goods_price,goods.goods_marketprice,goods.goods_salenum,goods.store_id,goods.goods_id";
        $goods_arr = Model()->table('mb_goods_tags_goods,goods')->join('inner')->on('mb_goods_tags_goods.goods_id=goods.goods_id')->where("tag_id=$tag_id and goods_state = 1 and goods_verify = 1")->field($field)->order('mb_goods_tags_goods.sort asc')->select();
        foreach ($goods_arr as $key => $v) {
            $goods_arr[$key]['goods_image'] = cthumb($v['goods_image'], 360, $v['store_id']);
        }
        $data['goods_list'] = $goods_arr;
        output_data($data);
    }

    /**
     *  伙拼专题活动
     */
    public function  for_huopinOp(){
        //$active_name = '四月份活动伙拼专题';

        $member_info=$this->getMemberInfo();
        $redtype = $_POST['redtype'];
        $active_name           = trim($_POST['active_name']);
        $is_redpack            = !empty($_POST['is_redpack']) ? $_POST['is_redpack'] : 0;

        $data = array();
        $data['redpack_list'] = [];
        if($is_redpack == 1){
            $redpack_ten_name      = trim($_POST['redpack_ten_name']);
            $redpack_nineteen_name = trim($_POST['redpack_nineteen_name']);
            //十点红包模板
            $redpack_ten_id = $this->getredpack_id($redpack_ten_name);
            //十九点红包模板
            $redpack_nineteen_id = $this->getredpack_id($redpack_nineteen_name);
            //是否显示10点开抢
            if(10>date('H')){
                $data['ten_show_status'] =1;
            }
            if(19>date('H')){
                $data['nineteen_show_status'] = 1;
            }
            if($redtype=='nine'){
                $redpack_id = $redpack_nineteen_id;
            }else{
                $redpack_id = $redpack_ten_id;
            }
            $redpack_list = $this->time_redpack_list($redpack_id,$member_info);
            foreach($redpack_list  as $key=>$v){
                $redpack_list[$key]['rpacket_t_price'] = floatval($v['rpacket_t_price']);
                $redpack_list[$key]['rpacket_t_limit'] = floatval($v['rpacket_t_limit']);
            }
            if(!$redtype){
                if(19<=date('H')){
                    $redtype='nine';
                }
            }
            //用户切换优先
            if($redtype=='nine'){
                $data['redpack_list'] = $redpack_list;
                $data['redtype'] = 'nine';
            }else if($redtype=='ten'){
                $data['redpack_list'] = $redpack_list;
                $data['redtype'] = 'ten';
            }else{
                //用户未切换已当前时间进行默认
                if(19<=date('H')){
                    $data['redpack_list'] = $redpack_list;
                    $data['redtype']='nine';
                }elseif(10<=date('H')){
                    $data['redpack_list'] = $redpack_list;
                    $data['redtype']='ten';
                }else{
                    $data['redpack_list'] = $redpack_list;
                }
            }
        }

        $tag_id = $this->get_tagid($active_name);
        $field="p_pintuan_goods.goods_image,p_pintuan_goods.goods_name,p_pintuan_goods.goods_price,p_pintuan_goods.pintuan_price,p_pintuan_goods.min_num,p_pintuan_goods.goods_id,p_pintuan_goods.pintuan_goods_id,p_pintuan_goods.store_id,p_pintuan_goods.generalize_pic";
        $huopin_list = Model()->table('mb_goods_tags_goods,p_pintuan_goods')->join('inner')->
        on('mb_goods_tags_goods.pintuan_goods_id=p_pintuan_goods.pintuan_goods_id')
            ->where("tag_id=$tag_id and p_pintuan_goods.state=1 and p_pintuan_goods.start_time<".time()." and p_pintuan_goods.end_time>".time())
            ->field($field)->order('mb_goods_tags_goods.sort asc')->select();
        foreach($huopin_list as $key=>$v){
            $huopin_list[$key]['goods_image'] = UPLOAD_SITE_URL . '/shop/active/'.$v['generalize_pic'];
            $huopin_list[$key]['min_num'] = $v['min_num']-1;
        }
        $data['huopin_list'] = $huopin_list;
        output_data($data);
    }

    /**
     * 获取红包id
     */
    private function getredpack_id($redpack_name)
    {
        $redpack_id = Model()->table('spree')->where(array('spree_name' => $redpack_name))->find()['redpack_id'];
        return $redpack_id;
    }

    /**
     * 不同时间段不同红包的列表
     */
    private function time_redpack_list($redpack_id, $member_info)
    {
        $field        = "rpacket_t_id,rpacket_t_price,rpacket_t_limit,rpacket_t_total,rpacket_t_giveout,rpacket_t_eachlimit,rpacket_t_today_limit";
        $redpack_list = Model()->table('redpacket_template')->where("rpacket_t_state=1 and rpacket_t_id in ($redpack_id) and rpacket_t_start_date<=" . time() . ' and rpacket_t_end_date>=' . time())->field($field)->select();
        $huopin_ten   = array();
        foreach ($redpack_list as $key => $v) {
            if ($member_info) {
                $redpack_arr                          = $this->member_redpack_status($v, $member_info['member_id']);
                $redpack_list[$key]['redpack_status'] = $redpack_arr['status'];
                $redpack_list[$key]['redpack_msg']    = $redpack_arr['msg'];
            } else {
                $redpack_list[$key]['redpack_status'] = 3;
                $redpack_list[$key]['redpack_msg']    = '马上领取';
            }
        }
        return $redpack_list;
    }

    /**
     * 获取tagid
     */
    private function get_tagid($active_name)
    {
        $tag_id = Model()->table('mb_goods_tags')->where(array('tag_name' => $active_name))->find()['tag_id'];
        return $tag_id;
    }
}