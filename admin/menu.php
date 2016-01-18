<?php
/**
 * ****************************************************************************
 * Birthday - MODULE FOR XOOPS
 * Script made by Hervé Thouzard (http://www.herve-thouzard.com/)
 * Created on 10 jully. 08 at 11:32:40
 * ****************************************************************************
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$dirname = basename(dirname(dirname(__FILE__)));
$module_handler = xoops_gethandler('module');
$module = $module_handler->getByDirname($dirname);
$pathIcon32 = $module->getInfo('icons32');

$adminmenu = array();
$i = 1;
$adminmenu[$i]["title"] = _MI_BIRTHDAY_HOME;
$adminmenu[$i]["link"]  = "admin/index.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/home.png';
$i++;
$adminmenu[$i]["title"] = _MI_BIRTHDAY_BIRTHDAYS;
$adminmenu[$i]["link"]  = "admin/main.php";
$adminmenu[$i]["icon"] = './images/cake.png';
//$i++;
//$adminmenu[$i]["title"] = _MI_BIRTHDAY_MAINTAIN;
//$adminmenu[$i]["link"]  = "admin/main.php?op=maintain";
//$adminmenu[$i]["icon"] = './images/maintenance.png';
$i++;
$adminmenu[$i]["title"] =_MI_BIRTHDAY_ABOUT;
$adminmenu[$i]["link"]  = "admin/about.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/about.png';
