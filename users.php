<?php
/**
 * ****************************************************************************
 * birthday - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com/)
 * Created on 10 juil. 08 at 19:41:07
 * Version : $Id:
 * ****************************************************************************
 */

/**
 * Affiche la liste de tous les utilisateurs (ou de tous les utilisateurs dont c'est l'anniversaire aujourd'hui)
 */
require 'header.php';
$xoopsOption['template_main'] = 'birthday_users.html';
require_once XOOPS_ROOT_PATH.'/header.php';
require_once XOOPS_ROOT_PATH.'/class/pagenav.php';

$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$limit = birthday_utils::getModuleOption('perpage');    // Nombre maximum d'éléments à afficher
$users = array();

if (isset($xoopsConfig) && file_exists(BIRTHDAY_PATH.'language/'.$xoopsConfig['language'].'/blocks.php')) {
    require_once BIRTHDAY_PATH.'language/'.$xoopsConfig['language'].'/blocks.php';
} else {
    require_once BIRTHDAY_PATH.'language/english/blocks.php';
}

if(isset($_GET['op']) && $_GET['op'] == 'today') {    // Les utilisateurs dont l'anniversaire est aujourd'hui
    $itemsCount = $hBdUsersBirthday->getTodayBirthdaysCount();
    if($itemsCount > $limit) {
        $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=today');
    }
    $users = $hBdUsersBirthday->getTodayBirthdays($start, $limit);
} else {    // Tous les utilisateurs
    $itemsCount = $hBdUsersBirthday->getAllUsersCount();
    if($itemsCount > $limit) {
        $pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start');
    }
    if(birthday_utils::getModuleOption('userslist_sortorder') == 1) {    // Sort by date
        $sort = 'birthday_date';
        $order = 'ASC';
    } else {
        $sort = 'birthday_lastname';
        $order = 'ASC';
    }
    $users = $hBdUsersBirthday->getAllUsers($start, $limit, $sort, $order);
}
if(count($users) > 0) {
    foreach($users as $user) {
        $xoopsTpl->append('birthday_users', $user->toArray());
    }
}
if(isset($pagenav) && is_object($pagenav)) {
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());
}
$pageTitle = _BIRTHDAY_USERS_LIST.' - '.birthday_utils::getModuleName();
$metaDescription = $pageTitle;
$metaKeywords = '';
birthday_utils::setMetas($pageTitle, $metaDescription, $metaKeywords);

$path = array(BIRTHDAY_URL.'users.php' => _BIRTHDAY_USERS_LIST);
$breadcrumb = birthday_utils::breadcrumb($path);
$xoopsTpl->assign('breadcrumb', $breadcrumb);
require_once XOOPS_ROOT_PATH.'/footer.php';
