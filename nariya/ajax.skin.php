<?php
include_once('./_common.php');

if ($is_admin || IS_DEMO) {
	;
} else {
	exit;
}

if (!isset($board['bo_table']) || !$board['bo_table'])
	exit;

$type = isset($_GET['type']) ? na_fid($_GET['type']) : '';
$skin = isset($_GET['skin']) ? na_fid($_GET['skin']) : '';

if (!$type || !$skin)
	exit;

include_once(NA_PATH.'/lib/option.lib.php');

$is_setup_skin = $board_skin_path.'/'.$type.'/'.$skin.'/setup.skin.php';
if(is_file($is_setup_skin)) {
	unset($boset);
	$idn = mt_rand(1000, 9000); // id Start
	@include_once($is_setup_skin);
}