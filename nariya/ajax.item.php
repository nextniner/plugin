<?php
include_once('./_common.php');
include_once(NA_PATH.'/_shop.php');

$ca_id = isset($_REQUEST['ca_id']) ? safe_replace_regex($_REQUEST['ca_id'], 'ca_id') : '';
$skin = isset($_REQUEST['skin']) ? safe_replace_regex($_REQUEST['skin'], 'skin') : '';

// 상품 리스트에서 다른 필드로 정렬을 하려면 아래의 배열 코드에서 해당 필드를 추가하세요.
if(isset($sort) && !in_array($sort, array('it_name', 'it_sum_qty', 'it_price', 'it_use_avg', 'it_use_cnt', 'it_update_time', 'it_type1', 'it_type2', 'it_type3', 'it_type4', 'it_type5')) ){
    $sort='';
}

$sql = " select * from {$g5['g5_shop_category_table']} where ca_id = '$ca_id' and ca_use = '1'  ";
$ca = sql_fetch($sql);
if (! (isset($ca['ca_id']) && $ca['ca_id']))
    exit;

// 본인인증, 성인인증체크
if(!$is_admin && $config['cf_cert_use']) {
    $msg = shop_member_cert_check($ca_id, 'list');
    if($msg)
        exit;
}

// 스킨경로
$skin_dir = G5_SHOP_SKIN_PATH;

if($ca['ca_skin_dir']) {
    if(preg_match('#^theme/(.+)$#', $ca['ca_skin_dir'], $match))
        $skin_dir = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/shop/'.$match[1];
    else
        $skin_dir = G5_PATH.'/'.G5_SKIN_DIR.'/shop/'.$ca['ca_skin_dir'];

    if(is_dir($skin_dir)) {
        $skin_file = $skin_dir.'/'.$ca['ca_skin'];

        if(!is_file($skin_file))
            $skin_dir = G5_SHOP_SKIN_PATH;
    } else {
        $skin_dir = G5_SHOP_SKIN_PATH;
    }
}

define('IS_AJAX_ITEM', true);
define('G5_SHOP_CSS_URL', str_replace(G5_PATH, G5_URL, $skin_dir));

// 상품 출력순서가 있다면
if ($sort != "")
	$order_by = $sort.' '.$sortodr.' , it_order, it_id desc';
else
	$order_by = 'it_order, it_id desc';

// 리스트 스킨
$skin_file = is_include_path_check($skin_dir.'/'.$ca['ca_skin']) ? $skin_dir.'/'.$ca['ca_skin'] : '';

if ($skin_file && file_exists($skin_file)) {

	// 총몇개 = 한줄에 몇개 * 몇줄
	$items = $ca['ca_list_mod'] * $ca['ca_list_row'];
	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($page < 1) $page = 1;
	// 시작 레코드 구함
	$from_record = ($page - 1) * $items;

	$list = new item_list($skin_file, $ca['ca_list_mod'], $ca['ca_list_row'], $ca['ca_img_width'], $ca['ca_img_height']);
	$list->set_category($ca['ca_id'], 1);
	$list->set_category($ca['ca_id'], 2);
	$list->set_category($ca['ca_id'], 3);
	$list->set_is_page(true);
	$list->set_order_by($order_by);
	$list->set_from_record($from_record);
	$list->set_view('it_img', true);
	$list->set_view('it_id', false);
	$list->set_view('it_name', true);
	$list->set_view('it_basic', true);
	$list->set_view('it_cust_price', true);
	$list->set_view('it_price', true);
	$list->set_view('it_icon', true);
	$list->set_view('sns', true);

	echo $list->run();
}