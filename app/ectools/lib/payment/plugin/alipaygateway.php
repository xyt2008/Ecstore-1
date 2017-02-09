<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * alipay纯网关交易接口（国内）
 * @auther shopex ecstore dev dev@shopex.cn
 * @version 0.1
 * @package ectools.lib.payment.plugin
 */
final class ectools_payment_plugin_alipaygateway extends ectools_payment_app implements ectools_interface_payment_app {

	/**
	 * @var string 支付方式名称
	 */
    public $name = '支付宝网关支付';
    /**
     * @var string 支付方式接口名称
     */
    public $app_name = '支付宝网关支付接口';
     /**
     * @var string 支付方式key
     */
    public $app_key = 'alipaygateway';
	/**
	 * @var string 中心化统一的key
	 */
	public $app_rpc_key = 'alipaygateway';
	/**
	 * @var string 统一显示的名称
	 */
    public $display_name = '支付宝网关支付';
    /**
	 * @var string 货币名称
	 */
    public $curname = 'CNY';
    /**
	 * @var string 当前支付方式的版本号
	 */
    public $ver = '1.0';
    /**
     * @var string 当前支付方式所支持的平台
     */
    public $platform = 'ispc';

	/**
	 * @var array 扩展参数
	 */
	public $supportCurrency = array("CNY"=>"01");

    /**
     * @var array 扩展参数，支持银行
     */
    public $supportBank = array(
        //信用卡
        'credit'=>array(
            'BOCB2C'=>array(
                'bank_key'=>'BOCB2C',
                'bank_name'=>'中国银行',
                'bank_pic'=>'/images/bank/bank_bocb2c.jpg',
            ),//中国银行
            'ICBCB2C'=>array(
                'bank_key'=>'ICBCB2C',
                'bank_name'=>'工商银行',
                'bank_pic'=>'/images/bank/bank_icbc.jpg',
            ),//工商银行
            'ABC'=>array(
                'bank_key'=>'ABC',
                'bank_name'=>'农业银行',
                'bank_pic'=>'/images/bank/bank_abc.jpg',
            ),//农业银行
            'CCB'=>array(
                'bank_key'=>'CCB',
                'bank_name'=>'建设银行',
                'bank_pic'=>'/images/bank/bank_ccb.jpg',
            ),//建设银行
            'CMB'=>array(
                'bank_key'=>'CMB',
                'bank_name'=>'招商银行',
                'bank_pic'=>'/images/bank/bank_cmb.jpg',
            ),//招商银行
            'CIB'=>array(
                'bank_key'=>'CIB',
                'bank_name'=>'兴业银行',
                'bank_pic'=>'/images/bank/bank_cib.jpg',
            ),//兴业银行
            'SPDB'=>array(
                'bank_key'=>'SPDB',
                'bank_name'=>'浦发银行',
                'bank_pic'=>'/images/bank/bank_spdb.jpg',
            ),//浦发银行
            'SPABANK'=>array(
                'bank_key'=>'SPABANK',
                'bank_name'=>'平安银行',
                'bank_pic'=>'/images/bank/bank_spabank.jpg',
            ),//平安银行
            'GDB'=>array(
                'bank_key'=>'GDB',
                'bank_name'=>'广发银行',
                'bank_pic'=>'/images/bank/bank_gdb.jpg',
            ),//广发银行
            'CITIC'=>array(
                'bank_key'=>'CITIC',
                'bank_name'=>'中信银行',
                'bank_pic'=>'/images/bank/bank_citic.jpg',
            ),//中信银行
            'SHBANK'=>array(
                'bank_key'=>'SHBANK',
                'bank_name'=>'上海银行',
                'bank_pic'=>'/images/bank/bank_shbank.jpg',
            ),//上海银行
       //   'abc1003'=>'abc1003',//visa
       //   'abc1004'=>'abc1004',//master
       //   'POSTGC'=>'POSTGC',//邮政储蓄'
        ),
        //借记卡
        'debit'=>array(
            'BOC-DEBIT'=>array(
                'bank_key'=>'BOC-DEBIT',
                'bank_name'=>'中国银行',
                'bank_pic'=>'/images/bank/bank_bocb2c.jpg',
            ),//中国银行
            'ICBC-DEBIT'=>array(
                'bank_key'=>'ICBC-DEBIT',
                'bank_name'=>'工商银行',
                'bank_pic'=>'/images/bank/bank_icbc.jpg',
            ),//工商银行
            'BJBANK'=>array(
                'bank_key'=>'BJBANK',
                'bank_name'=>'北京银行',
                'bank_pic'=>'/images/bank/bank_bjbank.jpg',
            ),//北京银行
            'CCB-DEBIT'=>array(
                'bank_key'=>'CCB-DEBIT',
                'bank_name'=>'建设银行',
                'bank_pic'=>'/images/bank/bank_ccb.jpg',
            ),//建设银行
            'CMB-DEBIT'=>array(
                'bank_key'=>'CMB-DEBIT',
                'bank_name'=>'招商银行',
                'bank_pic'=>'/images/bank/bank_cmb.jpg',
            ),//招商银行
            'SPDB-DEBIT'=>array(
                'bank_key'=>'SPDB-DEBIT',
                'bank_name'=>'浦发银行',
                'bank_pic'=>'/images/bank/bank_spdb.jpg',
            ),//浦发银行
            'SPA-DEBIT'=>array(
                'bank_key'=>'SPA-DEBIT',
                'bank_name'=>'平安银行',
                'bank_pic'=>'/images/bank/bank_spabank.jpg',
            ),//平安银行
            'GDB-DEBIT'=>array(
                'bank_key'=>'GDB-DEBIT',
                'bank_name'=>'广发银行',
                'bank_pic'=>'/images/bank/bank_gdb.jpg',
            ),//广发银行
            'CEB-DEBIT'=>array(
                'bank_key'=>'CEB-DEBIT',
                'bank_name'=>'广大银行',
                'bank_pic'=>'/images/bank/bank_cebbank.jpg',
            ),//光大银行
            'PSBC-DEBIT'=>array(
                'bank_key'=>'PSBC-DEBIT',
                'bank_name'=>'邮政储蓄',
                'bank_pic'=>'/images/bank/bank_gdb.jpg',
            ),//邮政储蓄
            'COMM'=>array(
                'bank_key'=>'COMM',
                'bank_name'=>'广发银行',
                'bank_pic'=>'/images/bank/bank_comm.jpg',
            ),//交通银行'
        ),
    );

        /**
         * 构造方法
         * @param null
         * @return boolean
         */
        public function __construct($app){
            parent::__construct($app);

            //$this->callback_url = $this->app->base_url(true)."/apps/".basename(dirname(__FILE__))."/".basename(__FILE__);
            $this->notify_url = kernel::openapi_url('openapi.ectools_payment/parse/' . $this->app->app_id . '/ectools_payment_plugin_alipaygateway_server', 'callback');
            if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->notify_url, $matches))
            {
                $this->notify_url = str_replace('http://','',$this->notify_url);
                $this->notify_url = preg_replace("|/+|","/", $this->notify_url);
                $this->notify_url = "http://" . $this->notify_url;
            }
            else
            {
                $this->notify_url = str_replace('https://','',$this->notify_url);
                $this->notify_url = preg_replace("|/+|","/", $this->notify_url);
                $this->notify_url = "https://" . $this->notify_url;
            }
            $this->callback_url = kernel::openapi_url('openapi.ectools_payment/parse/' . $this->app->app_id . '/ectools_payment_plugin_alipaygateway', 'callback');
            if (preg_match("/^(http):\/\/?([^\/]+)/i", $this->callback_url, $matches))
            {
                $this->callback_url = str_replace('http://','',$this->callback_url);
                $this->callback_url = preg_replace("|/+|","/", $this->callback_url);
                $this->callback_url = "http://" . $this->callback_url;
            }
            else
            {
                $this->callback_url = str_replace('https://','',$this->callback_url);
                $this->callback_url = preg_replace("|/+|","/", $this->callback_url);
                $this->callback_url = "https://" . $this->callback_url;
            }
            //$this->submit_url = 'https://www.alipay.com/cooperate/gateway.do?_input_charset=utf-8';
        //ajx  按照相应要求请求接口网关改为一下地址
        $this->submit_url = 'https://mapi.alipay.com/gateway.do?_input_charset=utf-8';
        $this->submit_method = 'POST';
        $this->submit_charset = 'utf-8';
    }

    /**
     * 后台支付方式列表关于此支付方式的简介
     * @param null
     * @return string 简介内容
     */
    public function admin_intro(){
        $regIp = isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['HTTP_HOST'];
        return '<img src="' . $this->app->res_url . '/payments/images/ALIPAY.gif"><br /><b style="font-family:verdana;font-size:13px;padding:3px;color:#000"><br>ShopEx联合支付宝推出优惠套餐：无预付/年费，单笔费率低至0.7%-1.2%，无流量限制。</b><div style="padding:10px 0 0 388px"><a  href="javascript:void(0)" onclick="document.ALIPAYFORM.submit();"><img src="' . $this->app->res_url . '/payments/images/alipaysq.png"></a></div><div>如果您已经和支付宝签约了其他套餐，同样可以点击上面申请按钮重新签约，即可享受新的套餐。<br>如果不需要更换套餐，请将签约合作者身份ID等信息在下面填写即可，<a href="http://www.shopex.cn/help/ShopEx48/help_shopex48-1235733634-11323.html" target="_blank">点击这里查看使用帮助</a><form name="ALIPAYFORM" method="GET" action="http://top.shopex.cn/recordpayagent.php" target="_blank"><input type="hidden" name="postmethod" value="GET"><input type="hidden" name="payagentname" value="支付宝"><input type="hidden" name="payagentkey" value="ALIPAY"><input type="hidden" name="market_type" value="from_agent_contract"><input type="hidden" name="customer_external_id" value="C433530444855584111X"><input type="hidden" name="pro_codes" value="6AECD60F4D75A7FB"><input type="hidden" name="regIp" value="'.$regIp.'"><input type="hidden" name="domain" value="'.$this->app->base_url(true).'"></form></div>';
    }
    /**
     * 后台配置参数
     */
    public function setting(){
        return array(
            'pay_name' => array(
                'title' => app::get('ectools')->_('支付方式名称'),
                'type' => 'string',
                'validate_type' => 'required',
            ),
            'mer_id' => array(
                'title'=>app::get('ectools')->_('合作者身份(parterID)'),
                'type'=>'string',
                'validate_type' => 'required',
            ),
            'mer_key'=>array(
                'title'=>app::get('ectools')->_('交易安全校验码(key)'),
                'type'=>'string',
                'validate_type' => 'required',
            ),
            'order_by' =>array(
                'title'=>app::get('ectools')->_('排序'),
                'type'=>'string',
                'label'=>app::get('ectools')->_('整数值越小,显示越靠前,默认值为1'),
            ),
            'support_cur'=>array(
                'title'=>app::get('ectools')->_('支持币种'),
                'type'=>'text hidden cur',
                'options'=>$this->arrayCurrencyOptions,
            ),
            'pay_method'=>array(
                'title'=>app::get('ectools')->_('是否网关支付'),
                'type'=>'radio',
                //'options'=>array('bankPay'=>app::get('ectools')->_('网银支付'),'directPay'=>app::get('ectools')->_('支付宝余额支付')),
                'options'=>array('true'=>app::get('ectools')->_('是'),'false'=>app::get('ectools')->_('否')),
            ),
            'support_bank'=>array(
                'title'=>app::get('ectools')->_('支持的银行'),
                'type'=>'checkbank',
                'options'=>$this->supportBank,
            ),
            'pay_fee'=>array(
                'title'=>app::get('ectools')->_('交易费率'),
                'type'=>'pecentage',
                'validate_type' => 'number',
            ),
            'pay_brief'=>array(
                'title'=>app::get('ectools')->_('支付方式简介'),
                'type'=>'textarea',
            ),
            'pay_type'=>array(
                'title'=>app::get('ectools')->_('支付类型（是否在线支付）'),
                'type'=>'radio',
                'options'=>array('false'=>app::get('ectools')->_('否'),'true'=>app::get('ectools')->_('是')),
                'name' => 'pay_type',
            ),
            'status'=>array(
                'title'=>app::get('ectools')->_('是否开启此支付方式'),
                'type'=>'radio',
                'options'=>array('false'=>app::get('ectools')->_('否'),'true'=>app::get('ectools')->_('是')),
                'name' => 'status',
            ),
        );
    }

	/**
     * 前台支付方式列表关于此支付方式的简介
     * @param null
     * @return string 简介内容
     */
    public function intro(){
        return app::get('ectools')->_('支付宝（中国）网络技术有限公司是国内领先的独立第三方支付平台，由阿里巴巴集团创办。支付宝致力于为中国电子商务提供“简单、安全、快速”的在线支付解决方案。').'
<a target="_blank" href="https://www.alipay.com/static/utoj/utojindex.htm">'.app::get('ectools')->_('如何使用支付宝支付？').'</a>';
    }

    public function dopay($payment){
        $mer_id = $this->getConf('mer_id', __CLASS__);
        $mer_key = $this->getConf('mer_key', __CLASS__);
        $pay_method = $this->getConf('pay_method', __CLASS__);
        var_dump($pay_method);

        $params = array(
            //系统参数
            'service'=>'create_direct_pay_by_user',
            'partner'=>$mer_id,
            '_input_charset'=>'utf-8',
            'notify_url'=>$this->notify_url,
            'return_url'=>$this->callback_url,
//          'error_notify_url'=>'',
//业务参数
            'out_trade_no'=>$payment['payment_id'],
            'subject'=>$payment['body'],
            'payment_type'=>'1',
            'defaultbank'=>'CMB',
//          'seller_account_name'=>'',
//          'price'=>$payment['cur_money'],
//          'quantity'=>'',
            'total_fee'=>$payment['cur_money'],
            'body'=>$payment['body'],
//          'show_url'=>,
            'paymethod'=>$paymethod,
            ''
        );
        foreach($params as $k=>$v)
        {
            $this->add_field($k,$v);
        }
        $sign=$this->_get_mac($mer_key);
        $this->add_field('sign_type','MD5');
        $this->add_field('sign',$sign);

        echo '<pre>';print_r($payment);
        echo '<br>';print_r($params);
        exit;
        return null;
    }

    public function is_fields_valiad(){
        return null;
    }

    public function callback(&$recv){
        return null;
    }

    public function gen_form(){
        return null;
    }

    /**
     * 生成签名
     * @param mixed $form 包含签名数据的数组
     * @param mixed $key 签名用到的私钥
     * @access private
     * @return string
     */
    public function _get_mac($key){
        ksort($this->fields);
        reset($this->fields);
        $mac= "";
        foreach($this->fields as $k=>$v){
            $mac .= "&{$k}={$v}";
        }
        $mac = substr($mac,1);
        $mac = md5($mac.$key);  //验证信息
        return $mac;
    }
}
