<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
$db['archive_order_delivery']=array (
  'columns' => 
  array (
    'order_id' => 
    array (
      'type' => 'table:archive_orders:order_id',
      'required' => true,
      'virtual_pkey' => true,
      'default' => 0,
      'editable' => false,
      'comment' => app::get('b2c')->_('订单ID'),
    ),
    'dlytype' => 
    array (
      'type' => 
      array (
        'delivery' => app::get('b2c')->_('发货单'),
        'reship' => app::get('b2c')->_('退货单'),
      ),
      'default' => 'delivery',
      'required' => true,
      'label' => app::get('b2c')->_('单据类型'),
      'width' => 75,
      'editable' => false,
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
    ),
    'dly_id' => 
    array (
      'type' => 'varchar(20)',
      'virtual_pkey' => true,
      'required' => true,
      'label' => app::get('b2c')->_('关联单号'),
      'width' => 110,
      'editable' => false,
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'filterdefault' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'items' => 
    array (
      'type' => 'text',
      'label' => app::get('b2c')->_('货品明细'),
      'editable' => false,
    ),
  ),
  'version' => '$Rev: 41996 $',
  'comment' => app::get('b2c')->_('发货/退货单'),
);
