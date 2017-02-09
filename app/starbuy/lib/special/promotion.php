<?php

class starbuy_special_promotion
{
	function __construct($app)
	{
		$this->app = $app;
		$this->mdl_product = app::get('b2c')->model('products');
	}
	//团购排他
	function extend_filter($filter)
	{
		
        $mdl_preparesell = app::get('preparesell')->model('preparesell_goods');
		$mdl_starbuy = app::get('starbuy')->model('special_goods');
		$mdl_gift = app::get('gift')->model('ref');
        $starbuy_product_id = $mdl_starbuy->getList('product_id',array('product_id|notin'=>$filter));
        $pre_product_id = $mdl_preparesell->getList('product_id');
        $gift_product_id = $mdl_gift->getList('product_id');
        $product_id=array_merge($pre_product_id,$gift_product_id,$starbuy_product_id);
		foreach ($product_id as $key => $value) {
			$products_id[]=$value['product_id'];
		}
		unset($filter['promotion']);
		$filter['product_id|notin'] = $products_id;
        return $filter;

	}
}
