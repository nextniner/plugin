<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(NA_PATH.'/_shop.php');

if( isset($sfl) && ! in_array($sfl, array('b.it_name', 'a.it_id', 'a.is_subject', 'a.is_content', 'a.is_name', 'a.mb_id')) ){
    //다른값이 들어가있다면 초기화
    $sfl = '';
}

$g5['title'] = '사용후기';
include_once(G5_SHOP_PATH.'/_head.php');

// 신고글 체크
$singo_write = na_singo_array('iuse');
$singo_count = count($singo_write);

// 차단회원 체크
$chadan_list = ($is_member && isset($member['as_chadan']) && trim($member['as_chadan'])) ? na_explode(',', $member['as_chadan']) : array();
$chadan_count = count($chadan_list);

// 차단회원글 제외
$sql_where = '';
if ($chadan_count)
	$sql_where .= na_sql_find('a.mb_id', trim($member['as_chadan']), 1);

// 신고글 제외
if ($singo_count)
	$sql_where .= na_sql_find('a.is_id', implode(',', $singo_write), 1);

$sql_common = " from `{$g5['g5_shop_item_use_table']}` a join `{$g5['g5_shop_item_table']}` b on (a.it_id=b.it_id) ";
$sql_search = " where a.is_confirm = '1' $sql_where ";

if(!$sfl)
    $sfl = 'b.it_name';

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "a.it_id" :
            $sql_search .= " ($sfl like '$stx%') ";
            break;
        case "a.is_name" :
        case "a.mb_id" :
            $sql_search .= " ($sfl = '$stx') ";
            break;
        default :
            $sql_search .= " ($sfl like '%$stx%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "a.is_id";
    $sod = "desc";
}
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt
         $sql_common
         $sql_search
         $sql_order ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
          $sql_common
          $sql_search
          $sql_order
          limit $from_record, $rows ";
$result = sql_query($sql);

$itemuselist_skin = G5_SHOP_SKIN_PATH.'/itemuselist.skin.php';

if(!file_exists($itemuselist_skin)) {
    echo str_replace(G5_PATH.'/', '', $itemuselist_skin).' 스킨 파일이 존재하지 않습니다.';
} else {
    include_once($itemuselist_skin);
}

include_once(G5_SHOP_PATH.'/_tail.php');