<?php
/**
 * ****************************************************************************
 * birthday - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com/)
 * Created on 11 juil. 08 at 14:53:56
 * Version : $Id:
 * ****************************************************************************
 */
function birthday_search($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB;
	include XOOPS_ROOT_PATH.'/modules/birthday/include/common.php';
	include_once XOOPS_ROOT_PATH.'/modules/birthday/class/users_birthday.php';

	// Recherche dans les produits
	$sql = 'SELECT birthday_id, birthday_firstname, birthday_lastname, birthday_date, birthday_uid FROM '.$xoopsDB->prefix('users_birthday').' WHERE (birthday_id <> 0 ';
	if ( $userid != 0 ) {
		$sql .= '  AND birthday_uid = '.$userid;
	}
	$sql .= ') ';

	$tmpObject = new users_birthday();
	$datas = $tmpObject->getVars();
	$tblFields = array();
	$cnt = 0;
	foreach($datas as $key => $value) {
		if($value['data_type'] == XOBJ_DTYPE_TXTBOX || $value['data_type'] == XOBJ_DTYPE_TXTAREA) {
			if($cnt == 0) {
				$tblFields[] = $key;
			} else {
				$tblFields[] = ' OR '.$key;
			}
			$cnt++;
		}
	}

	$count = count($queryarray);
	$more = '';
	if( is_array($queryarray) && $count > 0 ) {
		$cnt = 0;
		$sql .= ' AND (';
		$more = ')';
		foreach($queryarray as $oneQuery) {
			$sql .= '(';
			$cond = " LIKE '%".$oneQuery."%' ";
			$sql .= implode($cond, $tblFields).$cond.')';
			$cnt++;
			if($cnt != $count) {
				$sql .= ' '.$andor.' ';
			}
		}
	}
	$sql .= $more.' ORDER BY birthday_date DESC';
	$i = 0;
	$ret = array();
	$myts =& MyTextSanitizer::getInstance();
	$result = $xoopsDB->query($sql,$limit,$offset);
 	while ($myrow = $xoopsDB->fetchArray($result)) {
		$ret[$i]['image'] = 'images/crown.png';
		$ret[$i]['link'] = "user.php?birthday_id=".$myrow['birthday_id'];
		$ret[$i]['title'] = $myts->htmlSpecialChars($myrow['birthday_lastname'].' '.$myrow['birthday_firstname']);
		$ret[$i]['time'] = strtotime($myrow['birthday_date']);
		$ret[$i]['uid'] = $myrow['birthday_uid'];
		$i++;
	}
	return $ret;
}

?>