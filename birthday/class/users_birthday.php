<?php
/**
 * ****************************************************************************
 * birthday - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com/)
 * Created on 10 juil. 08 at 13:27:45
 * Version : $Id:
 * ****************************************************************************
 */
if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}

include_once XOOPS_ROOT_PATH.'/kernel/object.php';
//if (!class_exists('Birthday_XoopsPersistableObjectHandler')) {
//	include_once XOOPS_ROOT_PATH.'/modules/birthday/class/PersistableObjectHandler.php';
//}

class users_birthday extends XoopsObject //Birthday_Object
{
	function __construct()
	{
		$this->initVar('birthday_id',XOBJ_DTYPE_INT,null,false);
		$this->initVar('birthday_uid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('birthday_date',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('birthday_photo',XOBJ_DTYPE_TXTBOX,null,false);
        $this->initVar('birthday_description',XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('birthday_firstname',XOBJ_DTYPE_TXTBOX,null,false);
        $this->initVar('birthday_lastname',XOBJ_DTYPE_TXTBOX,null,false);
        $this->initVar('birthday_comments',XOBJ_DTYPE_INT,null,false);
		// Pour autoriser le html
		$this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
	}

	/**
	 * Retourne l'URL de l'image
	 * @return string	L'URL
	 */
	function getPictureUrl()
	{
		if(xoops_trim($this->getVar('birthday_photo')) != '') {
	        return birthday_utils::getModuleOption('folder_url').'/'.$this->getVar('birthday_photo');
		} else {
		    return '';
		}
	}

	/**
	 * Retourne le chemin de l'image
	 * @return string	Le chemin
	 */
	function getPicturePath()
	{
	    if(xoops_trim($this->getVar('birthday_photo')) != '') {
		    return birthday_utils::getModuleOption('folder_path').DIRECTORY_SEPARATOR.$this->getVar('birthday_photo');
	    } else {
	        return '';
	    }
	}

	/**
	 * Indique si l'image existe
	 *
	 * @return boolean	Vrai si l'image existe sinon faux
	 */
	function pictureExists()
	{
		$return = false;
		if(xoops_trim($this->getVar('birthday_photo')) != '' && file_exists(birthday_utils::getModuleOption('folder_path').DIRECTORY_SEPARATOR.$this->getVar('birthday_photo'))) {
			$return = true;
		}
		return $return;
	}

	/**
	 * Supprime l'image associée
	 * @return void
	 */
	function deletePicture()
	{
		if($this->pictureExists()) {
			@unlink(birthday_utils::getModuleOption('folder_path').DIRECTORY_SEPARATOR.$this->getVar('birthday_photo'));
		}
		$this->setVar('birthday_photo', '');
	}

	/**
	 * Rentourne la chaine à envoyer dans une balise <a> pour l'attribut href
	 *
	 * @return string
	 */
	function getHrefTitle()
	{
		return birthday_utils::makeHrefTitle(xoops_trim($this->getVar('birthday_lastname')).' '.xoops_trim($this->getVar('birthday_firstname')));
	}

    /**
     * Retourne l'utilisateur Xoops lié à l'enregistrement courant
     *
     */
	function getXoopsUser()
	{
        $ret = null;
	    static $member_handler;
	    if($this->getVar('birthday_uid') > 0) {
	        if(!isset($member_handler)) {
    	        $member_handler =& xoops_gethandler('member');
	        }
	        $user = $member_handler->getUser($this->getVar('birthday_uid'));
	        if(is_object($user)) {
	            $ret = $user;
	        }
	    }
	    return $ret;
	}

	function getFullName()
	{
	    return xoops_trim($this->getVar('birthday_lastname')).' '.xoops_trim($this->getVar('birthday_firstname'));
	}


	/**
	 * Retourne les éléments formatés pour affichage
	 *
	 * @param string $format	Le format à utiliser
	 * @return array	Les données formattées
	 */
	function toArray($format = 's')
    {
		$ret = array();
		foreach ($this->vars as $k => $v) {
			$ret[$k] = $this->getVar($k, $format);
		}
		$ret['birthday_full_imgurl'] = $this->getPictureUrl();
		$ret['birthday_href_title'] = $this->getHrefTitle();
		$user = null;
		$user = $this->getXoopsUser();
		if(is_object($user)) {
		    $ret['birthday_user_name'] = $user->getVar('name');
		    $ret['birthday_user_uname'] = $user->getVar('uname');
		    $ret['birthday_user_email'] = $user->getVar('email');
		    $ret['birthday_user_url'] = $user->getVar('url');
    		$ret['birthday_user_user_avatar'] = $user->getVar('user_avatar');
	    	$ret['birthday_user_user_from'] = $user->getVar('user_from');
		}
		$ret['birthday_formated_date'] = formatTimestamp(strtotime($this->getVar('birthday_date')), 's');
		$ret['birthday_fullname'] = $this->getFullName();
		return $ret;
    }
}


class BirthdayUsers_birthdayHandler extends XoopsPersistableObjectHandler //Birthday_XoopsPersistableObjectHandler
{
	function __construct($db)
	{	//								Table		    Classe		 	Id		        Description
		parent::__construct($db, 'users_birthday', 'users_birthday', 'birthday_id', 'birthday_lastname');
	}

    /**
     * Retourne un utilisateur à partir de son uid
     *
     * @param integer $uid	L'ID Xoops recherché
     * @return object
     */
	function getFromUid($uid)
    {
        $criteria = new Criteria('birthday_uid', intval($uid), '=');
        if($this->getCount($criteria) > 0) {
            $temp = array();
            $temp = $this->getObjects($criteria);
            if(count($temp) > 0) {
                return $temp[0];
            }
        }
        return $this->create(true);
    }

    /**
     * Création du formulaire de saisie
     *
     * @param users_birthday $item	L'élément à ajouter/modifier
     * @param string $baseurl	L'url de destination
     * @param boolean	$withUserSelect	Indique s'il faut inclure la liste de sélection de l'utilisateur
     * @param boolean	$captcha	Indique s'il faut utiliser un captcha
     * @return object	Le formulaire à utiliser
     */
	function getForm(users_birthday $item, $baseurl, $withUserSelect = true, $captcha = '')
	{
	    require_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
	    require_once XOOPS_ROOT_PATH.'/modules/birthday/class/formtextdateselect.php';

        global $xoopsModuleConfig;


        $edit = true;
        if($item->isNew()) {
            $edit = false;
        }

        if($edit) {
            $labelSubmit = _AM_BIRTHDAY_MODIFY;
            $title = _AM_BIRTHDAY_MODIFY_ITEM;
        } else {
            $labelSubmit = _AM_BIRTHDAY_ADD;
            $title = _AM_BIRTHDAY_ADD_ITEM;
        }
    	// Formulaire de création
    	$sform = new XoopsThemeForm($title, 'frmadd', $baseurl);
    	$sform->setExtra('enctype="multipart/form-data"');
    	$sform->addElement(new XoopsFormHidden('op', 'saveedit'));
    	$sform->addElement(new XoopsFormHidden('birthday_id', $item->getVar('birthday_id')));
        if($withUserSelect) {
            $selectUser = new XoopsFormSelectUser(_BIRTHDAY_USERNAME, 'birthday_uid', true, $item->getVar('birthday_uid', 'e'));
            $selectUser->setDescription(_AM_BIRTHDAY_USE_ANONYMOUS);
	        $sform->addElement($selectUser);
        }
	    $date = strtotime($item->getVar('birthday_date'));
        $sform->addElement(new XoopsFormTextDateSelect(_BIRTHDAY_DATE, 'birthday_date', 15, $date));
        $sform->addElement(new XoopsFormText(_BIRTHDAY_FIRSTNAME, 'birthday_firstname', 50,150, $item->getVar('birthday_firstname', 'e')), false);
        $sform->addElement(new XoopsFormText(_BIRTHDAY_LASTNAME, 'birthday_lastname',50,150, $item->getVar('birthday_lastname', 'e')), false);
//	    $editor = birthday_utils::getWysiwygForm(_BIRTHDAY_DESCRIPTION, 'birthday_description', $item->getVar('birthday_description', 'e'), 15, 60, 'description_hidden');
//	    if($editor) {
//            $sform->addElement($editor, false);
//        }
        $options_tray = new XoopsFormElementTray(_BIRTHDAY_DESCRIPTION, '<br />');
if (class_exists('XoopsFormEditor')) {
            $options['name'] = 'birthday_description';
            $options['value'] = $item->getVar('birthday_description', 'e');
            $options['rows'] = 25;
            $options['cols'] = '100%';
            $options['width'] = '100%';
            $options['height'] = '600px';
            $birthday_description = new XoopsFormEditor('', $xoopsModuleConfig['form_options'], $options, $nohtml = false, $onfailure = 'textarea');
            $options_tray->addElement($birthday_description);
        } else {
            $birthday_description = new XoopsFormDhtmlTextArea('', 'birthday_description', $item->getVar('birthday_description', 'e'), '100%', '100%');
            $options_tray->addElement($birthday_description);
        }
        $sform->addElement($options_tray);

    	if( $edit && trim($item->getVar('birthday_photo')) != '' && $item->pictureExists() ) {
		    $pictureTray = new XoopsFormElementTray(_AM_BIRTHDAY_CURRENT_PICTURE ,'<br />');
		    $pictureTray->addElement(new XoopsFormLabel('', "<img src='".$item->getPictureUrl()."' alt='' border='0' />"));
		    $deleteCheckbox = new XoopsFormCheckBox('', 'delpicture');
		    $deleteCheckbox->addOption(1, _DELETE);
		    $pictureTray->addElement($deleteCheckbox);
		    $sform->addElement($pictureTray);
		    unset($pictureTray, $deleteCheckbox);
        }
	    $sform->addElement(new XoopsFormFile(_AM_BIRTHDAY_PICTURE, 'attachedfile', birthday_utils::getModuleOption('maxuploadsize')), false);
	    if(xoops_trim($captcha) != '') {
	        $captcaField = new XoopsFormText(_BIRTHDAY_PLEASESOLVE, 'captcha', 30, 30, '');
	        $captcaField->setDescription($captcha);
            $sform->addElement($captcaField, true);
	    }

	    $button_tray = new XoopsFormElementTray('' ,'');
	    $submit_btn = new XoopsFormButton('', 'post', $labelSubmit, 'submit');
	    $button_tray->addElement($submit_btn);
	    $sform->addElement($button_tray);
	    $sform = birthday_utils::formMarkRequiredFields($sform);
	    return $sform;
	}

    /**
     * Enregistre un utilisateur après modification (ou ajout)
     *
     * @param boolean	$withCurrentUser	Indique s'il faut prendre l'utilisateur courant ou pas
     * @return boolean	Vrai si l'enregistrement a réussi sinon faux
     */
	function saveUser($withCurrentUser = false)
	{
	    global $destname;
        $images_width = birthday_utils::getModuleOption('images_width');
        $images_height = birthday_utils::getModuleOption('images_height');
        $id = isset($_POST['birthday_id']) ? intval($_POST['birthday_id']) : 0;
		if(!empty($id)) {
			$edit = true;
			$item = $this->get($id);
			if(!is_object($item)) {
				return false;
			}
			$item->unsetNew();
		} else {
			$edit = false;
			$item = $this->create(true);
		}
		$item->setVars($_POST);
		if($withCurrentUser) {
            global $xoopsUser;
            $item->setVar('birthday_uid', $xoopsUser->getVar('uid'));
		}
		if(isset($_POST['delpicture']) && intval($_POST['delpicture']) == 1) {
			if(trim($item->getVar('birthday_photo')) != '' && $item->pictureExists() ) {
				$item->deletePicture();
			}
			$item->setVar('birthday_photo', '');
		}

        $uploadFolder=birthday_utils::getModuleOption('folder_path');

		$return = birthday_utils::uploadFile(0,$uploadFolder );


		if($return === true) {
			$newDestName = birthday_utils::createUploadName($uploadFolder, basename($destname), true);
			$retval = birthday_utils::resizePicture($uploadFolder.DIRECTORY_SEPARATOR.$destname, $uploadFolder.DIRECTORY_SEPARATOR.$newDestName, $images_width, $images_height);
			if($retval == 1 || $retval == 3) {
				$item->setVar('birthday_photo', $newDestName);
			}
		} else {
			if($return !== false) {
				echo $return;
			}
		}
		$res = $this->insert($item);
		if($res) {
			birthday_utils::updateCache();
		}
		return $res;
	}

    /**
     * Suppression d'un utilisateur
     *
     * @param users_birthday $user	L'utilisateur à supprimer
     * @return boolean	Le résultat de la suppression
     */
	function deleteUser(users_birthday $user)
	{
	    $user->deletePicture();
	    $res = $this->delete($user, true);
		if($res) {
			birthday_utils::updateCache();
		}
		return $res;
	}

    /**
     * Mise à jour du compteur de commentaires pour un utilisateur
     *
     * @param intger $userId
     * @param integer $total_num
     * @return void
     */
	function updateCommentsCount($userId, $commentsCount)
	{
		$userId = intval($userId);
		$commentsCount = intval($commentsCount);
		$user = null;
		$user = $this->get($userId);
		if(is_object($user)) {
			$criteria = new Criteria('birthday_id', $userId, '=');
			$this->updateAll('birthday_comments', $commentsCount, $criteria, true);
		}
	}

    /**
     * Retourne les anniversaires du jour
     * @return array	Objets de type users_birthday
     */
	function getTodayBirthdays($start = 0, $limit = 0, $sort = 'birthday_lastname', $order = 'ASC')
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('day(birthday_date)', date('j'), '='));
		$criteria->add(new Criteria('month(birthday_date)', date('n'), '='));
		$criteria->setStart($start);
		if($limit > 0) {
		    $criteria->setLimit($limit);
        }
		$criteria->setSort($sort);
		$criteria->setOrder($order);
		return $this->getObjects($criteria);
	}

    /**
     * Retourne le nombre total d'anniversaires du jour
     * @return integer
     */
	function getTodayBirthdaysCount()
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('day(birthday_date)', date('j'), '='));
		$criteria->add(new Criteria('month(birthday_date)', date('n'), '='));
		return $this->getCount($criteria);
	}

    /**
     * Retourne le nombre total d'utilisateurs
     *
     * @return integer
     */
	function getAllUsersCount()
	{
	    return $this->getCount();
	}

    /**
     * Retourne la liste de tous les utilisateurs
     *
     * @param integer $start	Position de départ
     * @param integer $limit	Nombre maximum d'enregistrements
     * @param string $sort		Champ à utiliser pour le tri
     * @param string $order		Ordre de tri
     * @return array	Objets de type users_birthday
     */
	function getAllUsers($start = 0, $limit = 0, $sort = 'birthday_lastname', $order = 'ASC')
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('birthday_id', 0, '<>'));
		$criteria->setStart($start);
		if($limit > 0) {
		    $criteria->setLimit($limit);
        }
		$criteria->setSort($sort);
		$criteria->setOrder($order);
		return $this->getObjects($criteria);
	}
}
?>