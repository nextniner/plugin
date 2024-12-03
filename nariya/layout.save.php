<?php
include_once('./_common.php');

if ($is_admin === 'super') {
	;
} else {
    die('접근권한이 없습니다.');
}

$name = isset($_REQUEST['name']) ? na_fid($_REQUEST['name']) : '';
if(!$name)
	die('값이 제대로 넘어오지 않았습니다.');

$layout = (isset($_POST['layout']) && is_array($_POST['layout'])) ? $_POST['layout'] : array();

// 초기화
if(isset($_POST['freset']) && $_POST['freset']) {
	na_file_delete(NA_DATA_PATH.'/theme/'.$name.'.php');
	die();
}

// 저장하기
na_file_var_save(NA_DATA_PATH.'/theme/'.$name.'.php', $layout);