<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class giftpackage_cart_object_giftpackage implements b2c_interface_cart_object{

    private $app;
    public $member_ident; // 用户标识
    private $oCartObject;

    public $intopromotion = true;

    /**
     * 构造函数
     *
     * @param $object $app  // service 调用必须的
     */
    public function __construct() {
        $this->app = app::get('giftpackage');
        $this->_order = array('get'=>1,'del'=>10);
        $this->arr_member_info = kernel::single('b2c_cart_objects')->get_current_member();
        $this->member_ident = kernel::single("base_session")->sess_id();

        $this->oCartObjects = app::get('b2c')->model('cart_objects');
        $this->cart_objects_goods = kernel::single("giftpackage_cart_object_goods");
        $this->mdl_cart = $this->app->model('giftpackage');

    }

    public function get_part_type() {
		return array('giftpackage');
	}

    /*
     * 排序值
     */
    public function get_order($_type='get')
    {
        return $this->_order[$_type];
    }
    #End Func

    public function get_type() {
        return 'giftpackage';
    }

    /**
     * 购物车是否需要验证库存
     * @param null
     * @return boolean true or false
     */
    public function need_validate_store() {
	return true;
    }

    /**
     * 添加购物车项(coupon)
     * @return array //
     */
    public function add_object($data) {
        $data = $data[$this->get_type()];
        if(empty($data)) return false;

        $objIdent = $this->_generateIdent($data); //制定到具体商品id todo
        $filter = $aSave = array(
               'obj_ident'    => $objIdent,
               'member_ident' => $this->member_ident,
               'obj_type'     => 'giftpackage',
             );


        $this->gen_data( $data );

        $aSave['params'] = array(
                               'id'  =>  $data['id'],
                               'products' => $data['products'],//礼包所选商品信息
                               'extends_params' => $data['extends_params'],
                                /**
                                 * array( array(goods_id,product_id))
                                 **/
                           );
        $aSave['quantity']     = 1;
        if ($aData = $this->oCartObjects->getList('*', $filter, 0, -1, -1)){
            $aSave['quantity'] += $aData[0]['quantity'];
        }

        if(($flag=$this->_check($aSave))!==true) return $flag;


        $flag = $this->oCartObjects->save($aSave);

        return $flag ? true : false;
    }

    public function update($sIdent,$quantity) {
        if( empty($sIdent) || empty($quantity) ) return false;
        $filter =  array(
                        'obj_ident' => $sIdent,
                        'member_ident' => $this->member_ident,
                        'obj_type' => 'giftpackage',
                    );
        $arr = $this->oCartObjects->getList( '*',$filter );
        if( !$arr || !is_array($arr) ) return false;
        reset( $arr );
        $arr = current( $arr );
        $arr['quantity'] = current($quantity);

        $arr_data = $arr;
        $arr = null;

        if(($flag=$this->_check($arr_data))!==true) return false;
        $flag = $this->oCartObjects->save($arr_data);

        return $flag ? true : false;
    }


    private function gen_data( &$data ) {
        $o = app::get('b2c')->model('products');
        if( !is_array($data['products']) ) return false;
        foreach ($data['products'] as $key => $value) {
            if( !$value['product_id'] && $value['goods_id'] ) {
                $arr = $o->getList( 'product_id',array('goods_id'=>$value['goods_id']) );
                if( !$arr || count($arr)>1 ) return false;
                reset( $arr );
                $arr = current($arr);
                $data['products'][$key]['product_id'] = $arr['product_id'];
            }
        }
    }

    /**
     * 指定的购物车礼包
     *
     * @param string $sIdent
     * @param boolean $rich        // 是否只取cart_objects中的数据 还是完整的sdf数据
     * @return array
     */
    public function get($sIdent = null,$rich = false) {
        if(empty($sIdent)) return $this->getAll($rich);

        $aResult = $this->oCartObjects->getList('*',array(
                                           'obj_ident' => $sIdent,
                                           'member_ident'=> $this->member_ident,
                                        ));
        if(empty($aResult)) return array();
        if($rich) {
            $aResult = $this->_get($aResult);
            $aResult = $aResult[0];
        }

        return $aResult;
    }

    public function _get($aData){

        $aInfo = $this->_get_basic($aData);
        if( !$aInfo ) return false;
        $aProductId = $aInfo['productid'];
        $products_store = $tmp_products_store = array();

        $aProducts = $this->_get_products($aProductId);

        $aPackages = $this->_get_package( $aInfo['packageid'] );

        $user_custom_service = kernel::service('b2c_cart_object_usercustom');

        //积份设置
        if(!isset($this->site_score_policy) && empty($this->site_score_policy)) {
            $this->site_score_policy = app::get('b2c')->getConf('site.get_policy.method');
        }

        foreach( $aData as $key => $row ) {
            if( !$aPackages[$row['params']['id']] ) {
                unset($aData[$key]);continue;
            }

            $aData[$key]['package'] = $aPackages[$row['params']['id']];
            $aData[$key]['package']['_store']['real'] = $aData[$key]['package']['_store']['store'] = $aData[$key]['package']['store'];
            if( $aData[$key]['package']['limitbuy_count'] ) $aData[$key]['package']['_store']['real'] = $aData[$key]['package']['limitbuy_count'];

            //用户自定信息
            if( is_object($user_custom_service) ) {
                $user_custom_service->process( $row['params']['extends_params'],$return );
                if( $return ) $aData[$key]['package']['price'] = $return;
            }

            $arr_goods = $arr_products = array();
            //商品不存在时删除购物车内信息
            foreach( $row['params']['products'] as $_k => $goods_info ) {

                if(empty($aProducts[$goods_info['product_id']])) {
                    unset($aData[$key]['params']['products'][$_k]);continue;
                }
                $aData[$key]['obj_items'][$_k] = $aProducts[$goods_info['product_id']];
                $aData[$key]['obj_items'][$_k]['quantity'] = $row['quantity'];
                $aData[$key]['subtotal_goods_price'] += $aProducts[$goods_info['product_id']]['json_price']['price']*$row['quantity'];
                $arr_goods[$_k] = $goods_info['goods_id'];
                $arr_products[$_k] = $goods_info['product_id'];

            }
            $this->_check_goods_with_get($aData[$key]['obj_items'], $arr_goods);
            $this->_check_products_with_get($aData[$key]['obj_items'], $arr_products);

            //礼包重量为空时 计算加入购物车中的商品重量
            if( empty($aData[$key]['package']['weight']) && $aData[$key]['package']['weight']!=='0' ) {
                if( is_array($aData[$key]['obj_items']) ) {
                    foreach( $aData[$key]['obj_items'] as $row ) {
                        $aData[$key]['package']['weight']  += $row['weight'];
                    }
                }
            }
            if($this->site_score_policy==1 && isset($aData[$key]['package']['score'])) {
                //后台设置不使用积分的情况
                $aData[$key]['package']['score'] = 0;
            }
        }
        $arr_products = array();

        return $aData;
    }

    private function _get_basic( $data )
    {
        $aInfo = array();
        if( !$data ) return false;
        foreach( $data as $row ) {
            if( !$this->_check( $row ) ) continue;
            if( !is_array($row['params']['products']) ) continue;
            foreach( $row['params']['products'] as $goods_info ) {
                $aInfo['productid'][] = $goods_info['product_id'];
            }
            $aInfo['packageid'][] = $row['params']['id'];
        }
        return $aInfo;
    }
    #End Func


    /*
     * return package info
     */
    private function _get_package( $arr_package_id )
    {
        $arr = $this->app->model('giftpackage')->getList( '*',$arr_package_id );
        if( !is_array($arr) ) return false;
        $return = array();
        //默认图片
        $imageDefault = app::get('image')->getConf('image.set');
        foreach( $arr as $row ) {
            if(!$row['image']) {
                $row['image'] = $imageDefault['S']['default_image'];
            }
            $row['price'] = $row['amount'];
            $return[$row['id']] = $row;
        }
        return $return;
    }
    #End Func

    private function _get_products( $data )
    {
        return $this->cart_objects_goods->_get_products($data);
    }
    #End Func


    protected function _check_goods_with_get( &$aData, $arr_goods_id ) {
        return $this->cart_objects_goods->_check_goods_with_get( $aData,$arr_goods_id );
    }

    protected function _check_products_with_get( &$aData, $arr_products ) {
        return $this->cart_objects_goods->_check_products_with_get( $aData,$arr_products, $this->get_type() );
    }




    // 购物车里的所有礼包
    public function getAll($rich = false) {

        if(kernel::single("b2c_cart_object_goods")->get_cart_status()) {
            #
        } else {
            $aResult= $this->oCartObjects->getList('*',array(
                                               'obj_type' => $this->get_type(),
                                               'member_ident'=> $this->member_ident,
                                           ));
        }

        if(empty($aResult)) return array();


        /***** 判断是否过期          开始*********/
        $filter['id'] = array();
        foreach((array)$aResult as $value){
            $filter['id'][] = (int)$value['params']['id'];
        }
        $pks = $this->mdl_cart->getList('id,stime,etime' , $filter);
        $now = time();
        $todelete = array();
        foreach($pks as $value) {
            if(($value['stime'] && $now < (int)$value['stime']) || ($value['etime'] && $now > (int)$value['etime'])) {
                $todelete[] = $value['id'];
            }
        }
        foreach((array)$aResult as $key => $value){
            if(in_array((int)$value['params']['id'] , $todelete )){
                $this->delete($value['obj_ident']);
                unset($aResult[$key]);
            }
        }
        /***** 判断是否过期      结束*********/

        if(!$rich) return $aResult;
        return $this->_get($aResult);
    }

    // 删除购物车中指定礼包
    public function delete($sIdent = null) {
        if(empty($sIdent)) return $this->deleteAll();
        // todo 如果dbeav中有delete方法邓 再悠修改下面
        return $this->oCartObjects->delete(array('member_ident'=>$this->member_ident, 'obj_ident'=>$sIdent, 'obj_type'=>$this->get_type()));
    }

    // 清空购物车中礼包数据
    public function deleteAll() {
        return $this->oCartObjects->delete(array('member_ident'=>$this->member_ident, 'obj_type'=>$this->get_type()));
    }

    public function remove_object_part($sIdent,$quantity) {
        return true;
    }


    // 统计购物车中礼包数据
    public function count(&$aData) {
        // 购物车中不存在goods商品
        if(empty($aData['object']['giftpackage'])) return false;
        $aResult = array(
                      'subtotal_weight'=>0,
                      'subtotal'=>0,
                      'subtotal_price'=>0,
                      'subtotal_consume_score'=>0,
                      'subtotal_gain_score'=>0,
                      'discount_amount_prefilter'=>0,
                      'discount_amount_order'=>0,
                      'discount_amount'=>0,
                      'items_quantity'=>0,
                      'items_count'=>0,
                   );

        foreach($aData['object']['giftpackage'] as &$row) {
            $this->_count( $row );
            $aResult['subtotal_consume_score'] = 0;
            $aResult['subtotal_gain_score'] += $row['subtotal_gain_score'];

            $aResult['subtotal'] += $row['subtotal'];
            $aResult['subtotal_price'] += $row['subtotal_price'];
            $aResult['subtotal_weight'] += $row['subtotal_weight'];
            $aResult['discount_amount_prefilter'] += $row['discount_amount_prefilter'];

            $aResult['discount_amount_order'] += $row['discount_amount_order'];
            $aResult['discount_amount'] += $row['discount_amount_cart'] ;
            $aResult['items_quantity'] += $row['quantity'];
            $aResult['items_count']++;
        }
        return $aResult;
    }

    public function _count( &$aData ) {
        $row = $aData['package'];
        $aData['subtotal_gain_score'] = $row['score']*$aData['quantity'];
        $aData['subtotal'] = $row['price']*$aData['quantity'];
        $aData['subtotal_price'] = $row['price']*$aData['quantity'];
        $aData['subtotal_weight'] = $row['weight']*$aData['quantity'];
        $aData['discount_amount_prefilter'] = 0;
        $aData['discount_amount_order'] = 0;
        $aData['discount_amount'] = 0;
        $aData['items_quantity'] = count($aData['obj_items']);
    }


    // todo 礼包添加到购物车中的数据检测在这里处理
    // 礼包的正确性 类型 是否已使用
    private function _check(&$aData) {
        $params = $aData['params'];
        //没有礼包id 或 商品
        if( !$params['products'] || !$params['id'] || !is_array($params['products']) ) return false;

        $o_model_giftpackage = $this->app->model('giftpackage');
        $arr = $o_model_giftpackage->dump( $params['id'] );

        //是否有礼包信息
        if( !$arr ) return false;

        //是否所有会员： n：会员等级是否符合
        if( $arr['alluser']!=='true' ) {
            if( !$this->arr_member_info['member_id'] ){
                $jump_to_url = app::get('site')->router()->gen_url( array('app'=>'b2c','ctl'=>'site_passport','act'=>'login','full'=>'true') );
                if($_POST['mini_cart']){
                    echo json_encode( array('url'=>$jump_to_url) );exit;
                }
                else{
                    kernel::single('base_controller')->splash( 'success',$jump_to_url );exit;
                }
            }//return 'redirect_login';
            if( !in_array($this->arr_member_info['member_lv'],explode(',',$arr['member_lv_ids'])) ) return '会员等级不符';
            $member_id = $this->arr_member_info['member_id'];
            $arr_order_ref = $this->app->model('order_ref')->getList( 'quantity',array('member_id'=>$member_id,'giftpackage_id'=>$params['id']) );
            $user_quantity = array_map('current',$arr_order_ref);
            $user_quantity[] = $aData['quantity'];

            $user_quantity = array_sum($user_quantity);

            if( $arr['limitbuy_count'] && $user_quantity > $arr['limitbuy_count'] ) return '超出每人限购数量！';
        }

        //礼包包含商品数量与实际购买的不符
        if( $arr['goods_count']!=count($params['products']) ) return '礼包包含商品数量与实际购买的不符';

        //礼包信息礼品非数组 类型为任选
        if( !is_array($arr['goods']) ) {
            if( $arr['type']!=='1' ) return false;
        }

        //超出限购数量
        if( $arr['limitbuy_count'] && $arr['limitbuy_count']<$aData['quantity'] ) return '购买的礼包数量超过最大限购数量';


        //验证整个购物车中的礼包数量
        $arr_cart_objects_giftpackage = $this->oCartObjects->getList('*',array('member_id'=>$aData['member_id'],'member_ident'=>$aData['member_ident'],'obj_type'=>$this->get_type()));

        if( $arr_cart_objects_giftpackage && is_array( $arr_cart_objects_giftpackage ) ) {
            $_check_quantity = $aData['quantity'];
            foreach( $arr_cart_objects_giftpackage as $arr_cart_objects_items ) {
                if( $arr_cart_objects_items['obj_ident']==$aData['obj_ident'] ) continue;
                if( $aData['params']['id']!=$arr_cart_objects_items['params']['id'] ) continue;
                $_check_quantity += $arr_cart_objects_items['quantity'];
            }
            if( $arr['limitbuy_count'] && $arr['limitbuy_count']<$_check_quantity ) return '购买的礼包数量超过最大限购数量!';
            if( $arr['store']<($arr['freez'] + $_check_quantity) ) return '购买的礼包数量超出库存!';
            if( $arr['limitbuy_count'] && $user_quantity > ($arr['limitbuy_count']-$_check_quantity+$aData['quantity']) ) return '超出每人限购数量！';
        }

        //超出库存
        if( $arr['store']<($arr['freez'] + $aData['quantity']) ) return '购买的礼包数量超出库存';

        $arr_goods_id = $arr_product_id = array();
		//  当前修改过的礼包的真实数量.
        foreach( $params['products'] as $row ) {
            $arr_goods_id[] = $row['goods_id'];
            $arr_product_id[] = $row['product_id'];
            $arr_goods_store[$row['goods_id']] += $aData['quantity'];
            $arr_product_store[$row['product_id']] += $aData['quantity'];
        }
        if( $arr_cart_objects_giftpackage && is_array( $arr_cart_objects_giftpackage ) ) {
            foreach( $arr_cart_objects_giftpackage as $k=>$arr_cart_objects_items) {
                foreach($arr_cart_objects_items['params']['products'] as $k=>$v) {
					//  当前修改过的数量的礼包不在处理了.
					if ($arr_cart_objects_items['obj_ident'] == $aData['obj_ident']) continue;
                    if(!in_array($v['goods_id'], $arr_goods_id)) {
                        $arr_goods_id[] = $v['goods_id'];
                    }
                    if(!in_array($v['product_id'], $arr_product_id)) {
                        $arr_product_id[] = $v['product_id'];
                    }
                    $arr_goods_store[$v['goods_id']] += $arr_cart_objects_items['quantity'];
                    $arr_product_store[$v['product_id']] += $arr_cart_objects_items['quantity'];
                }
            }
        }

        foreach( $arr_goods_store as $gid => $quantity ) {
            if( ($return=$this->_check_goods_with_add(array($gid),$quantity))!==true ) return $return;
        }

        foreach( $arr_product_store as $pid => $quantity ) {
            if( ($return=$this->_check_products_with_add(array($pid),$quantity))!==true ) return $return;
        }

        if( ($return=$this->_check_goods_with_add($arr_goods_id,$aData['quantity']))!==true ) return $return;
        if( ($return=$this->_check_products_with_add( $arr_product_id,$aData['quantity']))!==true ) return $return;

        #kernel::single("b2c_cart_object_goods")->getAll();

        return true;
    }

    private function _check_goods_with_add( $arr,$quantity ) {
        $return = $this->cart_objects_goods->_check_goods_with_add( $arr,$quantity );
        if( $return!==true ) {
            return $return;
        }
        return true;
    }

    private function _check_products_with_add( $arr,$quantity ) {
        $return = $this->cart_objects_goods->_check_products_with_add( $arr,$quantity );
        if( $return!==true ) {
            return $return;
        }
        return true;
    }

    private function _generateIdent($aData) {
        $ident = $this->get_type()."_".$aData['id'];
        if( $aData['products'] && is_array($aData['products']) ) {
            foreach( $aData['products'] as $row ) {
                $p[] = $row['product_id'];
            }
        }
        return $ident.($p ? implode('-',(array)$p) : '');
    }


    public function apply_to_disabled( $data,$session,$flag ) {
        foreach( (array)$data as $_key => $_val ) {
            if( !isset( $session[$_val['obj_ident']] ) ) continue;
            if( $session[$_val['obj_ident']]==='true' ) {
                $data[$_key]['disabled'] = 'true';
                $tmp[$_val['obj_ident']] = true;
            } else {
                foreach( (array)$session[$_val['obj_ident']] as $_type => $_sess_by_type ) {
                    if( !isset($_val[$_type]) ) continue;
                    foreach( (array)$_val[$_type] as $_item_key => $_item_val ) {
                        if( isset($_sess_by_type[$_item_val['product_id']]) && $_sess_by_type[$_item_val['product_id']]==='true' ) {
                            if( $flag ) {
                                $data[$_key][$_type][$_item_key]['disabled'] = 'true';
                            } else {
                                unset($data[$_key][$_type][$_item_key]);
                            }
                            $tmp[$_val['obj_ident']][$_type][$_item_val['product_id']] = true;
                        }
                    }
                }
            }
        }

        $this->unset_session( $session,$tmp );
        return $data;
    }

    /*
     * return update info
     */
    public function get_update_num( $arr,$index )
    {
        $o_currency = kernel::single('ectools_mdl_currency');
        foreach( $arr as $row ) {
            if( $row['obj_ident']!=$index['ident'] ) continue;
            ($row['subtotal_goods_price']-$row['subtotal']<=0)?$discount = 0:$discount=$row['subtotal_goods_price']-$row['subtotal'];
            return array(
                    'buy_price'=>$o_currency->changer_odr($row['subtotal']),
                    'consume_score'=>(float)($row['subtotal_gain_score']),
                    'discount' => $o_currency->changer_odr($discount),
                );
        }

    }
    #End Func
}
