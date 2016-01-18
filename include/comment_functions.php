<?php
/**
 * ****************************************************************************
 * birthday - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com/)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id:
 * ****************************************************************************
 */

if (!defined('XOOPS_ROOT_PATH')) {
    die("XOOPS root path not defined");
}

function birthday_com_update($userId, $total_num)
{
    include XOOPS_ROOT_PATH.'/modules/birthday/include/common.php';
    global $hBdUsersBirthday;
    if(!is_object($hBdUsersBirthday)) {
        $hBdUsersBirthday = xoops_getmodulehandler('users_birthday', BIRTHDAY_DIRNAME);
    }
    $hBdUsersBirthday->updateCommentsCount($userId, $total_num);
}

function birthday_com_approve(&$comment)
{
    // notification mail here
}
