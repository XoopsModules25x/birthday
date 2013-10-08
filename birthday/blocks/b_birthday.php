<?php
function b_birthday_show($options) {
	global $xoopsUser;
	$block = array();
    include XOOPS_ROOT_PATH.'/modules/birthday/include/common.php';
    $start = 0;
    $limit = intval($options[0]);
    $itemsCount = $hBdUsersBirthday->getTodayBirthdaysCount();
    $users = $hBdUsersBirthday->getTodayBirthdays($start, $limit);
    if(count($users) > 0) {
        foreach($users as $user) {
            $block['birthday_today_users'][] = $user->toArray();
        }
    }
    if($itemsCount > $limit) {
        $block['birthday_today_more'] = true;
    } else {
        $block['birthday_today_more'] = false;
    }

	if (is_object($xoopsUser) && birthday_utils::getModuleOption('enable_users')) {
		$block['birthday_today_mypage'] = true;
	} else {
	    $block['birthday_today_mypage'] = false;
	}
	return $block;
}

function b_birthday_edit($options)
{
	include XOOPS_ROOT_PATH.'/modules/birthday/include/common.php';
	$form = '';
	$form .= "<table border='0'>";
	$form .= '<tr><td>'._MB_BIRTHDAY_MAX_ITEMS."</td><td><input type='text' name='options[]' id='options' value='".$options[0]."' /></td></tr>\n";
	$form .= "</table>\n";
	return $form;
}
?>
