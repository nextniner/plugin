<?php
include_once('./_common.php');

if ($is_admin || IS_DEMO) {
	;
} else {
    die('접근권한이 없습니다.');
}

if (!isset($board['bo_table']) || !$board['bo_table'])
	die('잘못된 접근입니다.');

$type = isset($_GET['type']) ? na_fid($_GET['type']) : '';
$skin = isset($_GET['skin']) ? na_fid($_GET['skin']) : '';

if (!$type || !$skin)
	die('값이 없습니다.');

include_once(NA_PATH.'/lib/option.lib.php');

$is_setup_skin = $board_skin_path.'/'.$type.'/'.$skin.'/setup.skin.php';
if(file_exists($is_setup_skin)) {
	@include_once($is_setup_skin);
}