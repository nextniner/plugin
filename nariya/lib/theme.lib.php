<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// SEO 메타 출력
function na_seo($buffer) {

	if(!defined('_SEOMETA_'))
		return $buffer;

	ob_start();
	@include_once (NA_PATH.'/seo.meta.php');
	$meta = ob_get_contents();
	ob_end_clean();

	if(trim($meta)) {
		$nl = "\n";
		$buffer = preg_replace('#(<title>)#', "$meta{$nl}$0", $buffer);
	}

	return $buffer;
}

// 메뉴 생성
function na_menu() {
	global $nariya, $pset;

	$mid = (is_array($pset) && isset($pset['mid'])) ? $pset['mid'] : array();
	$uid = (is_array($pset) && isset($pset['uid'])) ? $pset['uid'] : '';
	$is_mid = empty($mid) ? false : true;

	// 메뉴 불러오기
	$nav = array();
	$me_prefix = (defined('_SHOP_')) ? 'shop-' : '';
	$me_file = (G5_IS_MOBILE) ? $me_prefix.'menu-mo.php' : $me_prefix.'menu-pc.php';
	$me = na_file_var_load(NA_DATA_PATH.'/'.$me_file);

	// 새글
	$post = na_post_new();
	$post = is_array($post) ? $post : array();

	// 메뉴 정리
	for($i=0; $i < count($me); $i++) {
		$on = 0;
		$new = 0;
		if($me[$i]['is_sub']) { //1차 서브
			for($j=0; $j < count($me[$i]['s']); $j++) {

				$on1 = 0;
				$new1 = 0;
				if($me[$i]['s'][$j]['is_sub']) { //2차 서브
					for($k=0; $k < count($me[$i]['s'][$j]['s']); $k++) {

						// 현재 위치
						$me_id = $me[$i]['s'][$j]['s'][$k]['id'];
						if($is_mid && $me_id && in_array($me_id, $mid)) {
							$me[$i]['s'][$j]['s'][$k]['on'] = true;
							$nav[] = array('href'=>$me[$i]['s'][$j]['s'][$k]['me_link'], 'target'=>$me[$i]['s'][$j]['s'][$k]['me_target'], 'text'=>$me[$i]['s'][$j]['s'][$k]['me_name']);
							$on1++;
						} else {
							$me[$i]['s'][$j]['s'][$k]['on'] = false;
						}

						// 동일 체크
						$me[$i]['s'][$j]['s'][$k]['eq'] = ($uid && $me_id == $uid) ? true : false;

						// 새게시물
						if($me[$i]['s'][$j]['s'][$k]['bo_table']) {
							$bokey = $me[$i]['s'][$j]['s'][$k]['bo_table'];
							if($me[$i]['s'][$j]['s'][$k]['sca']) {
								$catekey = $bokey.'-cate-count';
							    $catekey = md5($catekey);
								if (isset($$bokey[$catekey])) {
									$me[$i]['s'][$j]['s'][$k]['new'] = $$bokey[$catekey];
								} else {
									$$bokey = na_cate_new($bokey, array(), $nariya['new_cache']);
									$me[$i]['s'][$j]['s'][$k]['new'] = isset($$bokey[$catekey]) ? $$bokey[$catekey] : 0;
								}
							} else {
								$me[$i]['s'][$j]['s'][$k]['new'] = isset($post[$bokey]) ? $post[$bokey] : 0;
							}
						} else {
							$me[$i]['s'][$j]['s'][$k]['new'] = 0;
						}
						
						$new1 = $new1 + (int)$me[$i]['s'][$j]['s'][$k]['new'];
					}
				}

				// 현재 위치
				$me_id = $me[$i]['s'][$j]['id'];
				if($on1) {
					$me[$i]['s'][$j]['on'] = true;
					$nav[] = array('href'=>$me[$i]['s'][$j]['me_link'], 'target'=>$me[$i]['s'][$j]['me_target'], 'text'=>$me[$i]['s'][$j]['me_name']);
					$on++;
				} else {
					if($is_mid && $me_id && in_array($me_id, $mid)) {
						$me[$i]['s'][$j]['on'] = true;
						$nav[] = array('href'=>$me[$i]['s'][$j]['me_link'], 'target'=>$me[$i]['s'][$j]['me_target'], 'text'=>$me[$i]['s'][$j]['me_name']);
						$on++;
					} else {
						$me[$i]['s'][$j]['on'] = false;
					}
				}

				// 동일 체크
				$me[$i]['s'][$j]['eq'] = ($uid && $me_id == $uid) ? true : false;

				// 새게시물
				$me[$i]['s'][$j]['new'] = 0;
				if($me[$i]['s'][$j]['bo_table']) {
					$bokey = $me[$i]['s'][$j]['bo_table'];
					if($me[$i]['s'][$j]['sca']) {
						$catekey = $bokey.'-cate-new';
						$catekey = md5($catekey);
						if (isset($$bokey[$catekey])) {
							$me[$i]['s'][$j]['new'] = $$bokey[$catekey];
						} else {
							$$bokey = na_cate_new($bokey, array(), $nariya['new_cache']);
							$me[$i]['s'][$j]['new'] = isset($$bokey[$catekey]) ? $$bokey[$catekey] : 0;
						}
					} else {
						$me[$i]['s'][$j]['new'] = isset($post[$bokey]) ? $post[$bokey] : 0;
					}
				} 

				if(!$me[$i]['s'][$j]['new']) {
					$me[$i]['s'][$j]['new'] = ($new1) ? $new1 : 0;
				}
			
				$new = $new + (int)$me[$i]['s'][$j]['new'];
			}
		}
		
		// 현재 위치
		$me_id = $me[$i]['id'];
		if($on) {
			$me[$i]['on'] = true;
			$nav[] = array('href'=>$me[$i]['me_link'], 'target'=>$me[$i]['me_target'], 'text'=>$me[$i]['me_name']);
		} else {
			if($is_mid && $me_id && in_array($me_id, $mid)) {
				$me[$i]['on'] = true;
				$nav[] = array('href'=>$me[$i]['me_link'], 'target'=>$me[$i]['me_target'], 'text'=>$me[$i]['me_name']);
			} else {
				$me[$i]['on'] = false;
			}
		}

		// 동일 체크
		$me[$i]['eq'] = ($uid && $me_id == $uid) ? true : false;

		// 새게시물
		$me[$i]['new'] = 0;
		if($me[$i]['bo_table']) {
			$bokey = $me[$i]['bo_table'];
			if($me[$i]['sca']) {
				$catekey = $bokey.'-cate-new';
				$catekey = md5($catekey);
				if (isset($$bo_key[$catekey])) {
					$me[$i]['new'] = $$bokey[$catekey];
				} else {
					$$bokey = na_cate_new($bokey, array(), $nariya['new_cache']);
					$me[$i]['new'] = isset($$bokey[$catekey]) ? $$bokey[$catekey] : 0;
				}
			} else {
				$me[$i]['new'] = isset($post[$bokey]) ? $post[$bokey] : 0;
			}
		} 
		
		if(!$me[$i]['new']) {
			$me[$i]['new'] = ($new) ? $new : 0;
		}
	}

	if(!empty($nav))
		$nav = array_reverse($nav);

	return array($me, $nav);
}

function na_stats($time = 3) {

	$id = 'nariya-stats';
	$caches = false;
	$is_cache = (int)$time;
	if($is_cache > 0) {
		$caches = g5_get_cache($id, $is_cache * 60);
	}

	// 캐시값 넘김
	if(isset($caches['stats']) && is_array($caches['stats']))
		return $caches['stats'];

    global $g5, $config;

	$stats = array();

	// 현재 접속자
    $row = sql_fetch(" select sum(IF(mb_id<>'',1,0)) as mb_cnt, count(*) as total_cnt from {$g5['login_table']} where mb_id <> '{$config['cf_admin']}' ", false);
	$stats['now_total'] = isset($row['total_cnt']) ? (int)$row['total_cnt'] : 0;
	$stats['now_member'] = isset($row['mb_cnt']) ? (int)$row['mb_cnt'] : 0;

	// 전체회원
	$row1 = sql_fetch(" select count(*) as cnt from {$g5['member_table']} ", false); 
	$stats['join_total'] = isset($row1['cnt']) ? (int)$row1['cnt'] : 0;

	// 총 글수
	$row2 = sql_fetch(" select sum(bo_count_write) as w_cnt, sum(bo_count_comment) as c_cnt from {$g5['board_table']} where 1 ", false); 
	$stats['total_post'] = isset($row2['w_cnt']) ? (int)$row2['w_cnt'] : 0;
	$stats['total_comment'] = isset($row2['c_cnt']) ? (int)$row2['c_cnt'] : 0;

	// 방문통계는 그대로 출력
    // visit 배열변수에 $visit[1] = 오늘, $visit[2] = 어제, $visit[3] = 최대, $visit[4] = 전체 숫자가 들어감
    preg_match("/오늘:(.*),어제:(.*),최대:(.*),전체:(.*)/", $config['cf_visit'], $visit);

	$stats['visit_today'] = isset($visit[1]) ? (int)$visit[1] : 0;
	$stats['visit_yesterday'] = isset($visit[2]) ? (int)$visit[2] : 0;
	$stats['visit_max'] = isset($visit[3]) ? (int)$visit[3] : 0;
	$stats['visit_total'] = isset($visit[4]) ? (int)$visit[4] : 0;

	// 캐싱
	if($is_cache > 0) {
		$caches = array('stats' => $stats);
		g5_set_cache($id, $caches, $is_cache * 60);
	}

    return $stats;
}

//=====================================================================
// 영카트 쇼핑몰
//=====================================================================

// 쇼핑몰 카테고리
function na_shop_category() {
	global $nariya, $pset, $ca_id;

	$me = array();

	if(!IS_YC)
		return $me;

	$mid = (is_array($pset) && isset($pset['mid'])) ? $pset['mid'] : array();
	$is_mid = empty($mid) ? false : true;

	$shop_categories = get_shop_category_array(true);

	$ca_id1 = substr($ca_id,0,2);
	$ca_id2 = substr($ca_id,0,4);
	$ca_id3 = substr($ca_id,0,6);

	$i = 0;
	foreach($shop_categories as $cate1) {

		if(empty($cate1)) 
			continue;

		$row1 = $cate1['text'];

		$me[$i]['me_link'] = $row1['url'];
		$me[$i]['me_name'] = $row1['ca_name'];
		$me[$i]['me_target'] = '_self';
		$me[$i]['icon'] = $row1['ca_9'];
		$me[$i]['on'] = ($ca_id1 == $row1['ca_id']) ? true : false;
		$me[$i]['eq'] = ($ca_id == $row1['ca_id']) ? true : false;

		$count1 = ((int) count($cate1)) - 1;

		$me[$i]['is_sub'] = ($count1) ? true : false;
		
		$j = 0;
		foreach($cate1 as $key1=>$cate2) {
			if(empty($cate2) || $key1 === 'text')
				continue;
				
			$row2 = $cate2['text'];

			$me[$i]['s'][$j]['me_link'] = $row2['url'];
			$me[$i]['s'][$j]['me_name'] = $row2['ca_name'];
			$me[$i]['s'][$j]['me_target'] = '_self';
			$me[$i]['s'][$j]['icon'] = $row2['ca_9'];
			$me[$i]['s'][$j]['on'] = ($ca_id2 == $row2['ca_id']) ? true : false;
			$me[$i]['s'][$j]['eq'] = ($ca_id == $row2['ca_id']) ? true : false;

			$count2 = ((int) count($cate2)) - 1;

			$me[$i]['s'][$j]['is_sub'] = ($count2) ? true : false;

			$k = 0;
			foreach($cate2 as $key2=>$cate3) {
				if(empty($cate3) || $key2 === 'text')
					continue;

				$row3 = $cate3['text'];

				$me[$i]['s'][$j]['s'][$k]['me_link'] = $row3['url'];
				$me[$i]['s'][$j]['s'][$k]['me_name'] = $row3['ca_name'];
				$me[$i]['s'][$j]['s'][$k]['me_target'] = '_self';
				$me[$i]['s'][$j]['s'][$k]['icon'] = $row3['ca_9'];
				$me[$i]['s'][$j]['s'][$k]['on'] = ($ca_id3 == $row3['ca_id']) ? true : false;
				$me[$i]['s'][$j]['s'][$k]['eq'] = ($ca_id == $row3['ca_id']) ? true : false;

				$count3 = ((int) count($cate3)) - 1;

				$me[$i]['s'][$j]['s'][$k]['is_sub'] = ($count3) ? true : false;
				$k++;
			}
		
			$j++;
		}

		$i++;
	}

	return $me;
}