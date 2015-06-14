<?php

/**
 * Permet à l'utilisateur courant de modifier sa fiche (si l'option adéquate est activée)
 */
require 'header.php';
$xoopsOption['template_main'] = 'birthday_index.html';
require_once XOOPS_ROOT_PATH.'/header.php';
require XOOPS_ROOT_PATH.'/modules/birthday/include/function.php';

$baseurl = BIRTHDAY_URL.basename(__FILE__);    // URL de ce script
$uid = 0;
if (is_object($xoopsUser) && birthday_utils::getModuleOption('enable_users')) {
    $uid = intval($xoopsUser->getVar('uid'));
} else {
    birthday_utils::redirect(_BIRTHDAY_ERROR1, 'users.php', 4);
}

$op = isset($_POST['op']) ? $_POST['op'] : 'default';

switch($op) {
    case 'default':
        $item = $hBdUsersBirthday->getFromUid($uid);
        $captcha = '';
        if(birthday_utils::getModuleOption('use_captcha')) {
            require_once BIRTHDAY_PATH.'class/Numeral.php';
            $numcap = new birthday_Text_CAPTCHA_Numeral;
            $_SESSION['birthday_answer'] = $numcap->getAnswer();
            $captcha = $numcap->getOperation();
        }
        $form = $hBdUsersBirthday->getForm($item, $baseurl, false, $captcha);
        $xoopsTpl->assign('form', $form->render());
        break;

    case 'saveedit':
        if(birthday_utils::getModuleOption('use_captcha') && isset($_POST['captcha']) && isset($_SESSION['birthday_answer'])) {
            if ($_POST['captcha'] != $_SESSION['birthday_answer']) {
                birthday_utils::redirect(_BIRTHDAY_CAPTCHA_WRONG, 'index.php', 4);
            }
        }
        $result = $hBdUsersBirthday->saveUser(true);
        if($result) {
            birthday_utils::redirect(_AM_BIRTHDAY_SAVE_OK, 'users.php', 1);
        } else {
            birthday_utils::redirect(_AM_BIRTHDAY_SAVE_PB, $baseurl, 3);
        }
        break;
}
require_once XOOPS_ROOT_PATH.'/footer.php';
