<?php
/**
 * ****************************************************************************
 * birthday - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com/)
 * Created on 29 déc. 07 at 14:31:16
 * ****************************************************************************
 */

if (!defined('XOOPS_ROOT_PATH')) {
    die("XOOPS root path not defined");
}

/**
 * A set of useful and common functions
 *
 * @package birthday
 * @author Hervé Thouzard (http://www.herve-thouzard.com/)
 * @copyright (c) Instant Zero
 *
 * Note: You should be able to use it without the need to instanciate it.
 *
 */
class birthday_utils
{

    const MODULE_NAME = 'birthday';

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     * @static
     * @staticvar   object
     */
    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new birthday_utils();
        }

        return $instance;
    }

    /**
     * Returns a module's option (with cache)
     *
     * @param  string $option module option's name
     * @return mixed  option's value
     */
    function getModuleOption($option)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $repmodule = self::MODULE_NAME;
        static $tbloptions = array();
        if(is_array($tbloptions) && array_key_exists($option,$tbloptions)) {
            return $tbloptions[$option];
        }

        $retval = false;
        if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
            if(isset($xoopsModuleConfig[$option])) {
                $retval= $xoopsModuleConfig[$option];
            }
        } else {
            $module_handler =& xoops_gethandler('module');
            $module =& $module_handler->getByDirname($repmodule);
            $config_handler =& xoops_gethandler('config');
            if ($module) {
                $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
                if(isset($moduleConfig[$option])) {
                    $retval= $moduleConfig[$option];
                }
            }
        }
        $tbloptions[$option]=$retval;

        return $retval;
    }

    /**
     * Is Xoops 2.2.x ?
     *
     * @return boolean need to say it ?
     */
    function isX22()
    {
        $x22 = false;
        $xv = str_replace('XOOPS ','',XOOPS_VERSION);
        if(substr($xv,2,1) == '2') {
            $x22 = true;
        }

        return $x22;
    }

    /**
     * Is Xoops 2.3.x ?
     *
     * @return boolean need to say it ?
     */
    function isX23()
    {
        $x23 = false;
        $xv = str_replace('XOOPS ','',XOOPS_VERSION);
        if(substr($xv,2,1) == '3') {
            $x23 = true;
        }

        return $x23;
    }

    /**
     * Retreive an editor according to the module's option "form_options"
     *
     * @param  string $caption Caption to give to the editor
     * @param  string $name    Editor's name
     * @param  string $value   Editor's value
     * @param  string $width   Editor's width
     * @param  string $height  Editor's height
     * @return object The editor to use
     */
    function &getWysiwygForm($caption, $name, $value = '', $width = '100%', $height = '400px', $supplemental='')
    {
        $editor = false;
        $x22 = self::isX22();
        $editor_configs=array();
        $editor_configs['name'] =$name;
        $editor_configs['value'] = $value;
        $editor_configs['rows'] = 35;
        $editor_configs['cols'] = 60;
        $editor_configs['width'] = $width;
        $editor_configs['height'] = $height;

        $editor_option = self::getModuleOption('form_options');

        switch(strtolower($editor_option)) {
            case 'spaw':
                if(!$x22) {
                    if (is_readable(XOOPS_ROOT_PATH . '/class/spaw/formspaw.php'))    {
                        require_once(XOOPS_ROOT_PATH . '/class/spaw/formspaw.php');
                        $editor = new XoopsFormSpaw($caption, $name, $value);
                    }
                } else {
                    $editor = new XoopsFormEditor($caption, 'spaw', $editor_configs);
                }
                break;

            case 'fck':
                if(!$x22) {
                    if ( is_readable(XOOPS_ROOT_PATH . '/class/fckeditor/formfckeditor.php'))    {
                        require_once(XOOPS_ROOT_PATH . '/class/fckeditor/formfckeditor.php');
                        $editor = new XoopsFormFckeditor($caption, $name, $value);
                    }
                } else {
                    $editor = new XoopsFormEditor($caption, 'fckeditor', $editor_configs);
                }
                break;

            case 'htmlarea':
                if(!$x22) {
                    if ( is_readable(XOOPS_ROOT_PATH . '/class/htmlarea/formhtmlarea.php'))    {
                        require_once(XOOPS_ROOT_PATH . '/class/htmlarea/formhtmlarea.php');
                        $editor = new XoopsFormHtmlarea($caption, $name, $value);
                    }
                } else {
                    $editor = new XoopsFormEditor($caption, 'htmlarea', $editor_configs);
                }
                break;

            case 'dhtml':
                if(!$x22) {
                    $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, 10, 50, $supplemental);
                } else {
                    $editor = new XoopsFormEditor($caption, 'dhtmltextarea', $editor_configs);
                }
                break;

            case 'textarea':
                $editor = new XoopsFormTextArea($caption, $name, $value);
                break;

            case 'tinyeditor':
                if ( is_readable(XOOPS_ROOT_PATH.'/class/xoopseditor/tinyeditor/formtinyeditortextarea.php')) {
                    require_once XOOPS_ROOT_PATH.'/class/xoopseditor/tinyeditor/formtinyeditortextarea.php';
                    $editor = new XoopsFormTinyeditorTextArea(array('caption'=> $caption, 'name'=>$name, 'value'=>$value, 'width'=>'100%', 'height'=>'400px'));
                }
                break;

            case 'koivi':
                if(!$x22) {
                    if ( is_readable(XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php')) {
                        require_once(XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php');
                        $editor = new XoopsFormWysiwygTextArea($caption, $name, $value, '100%', '250px', '');
                    }
                } else {
                    $editor = new XoopsFormEditor($caption, 'koivi', $editor_configs);
                }
                break;
            }

            return $editor;
    }

    /**
     * Create (in a link) a javascript confirmation's box
     *
     * @param  string  $message Message to display
     * @param  boolean $form    Is this a confirmation for a form ?
     * @return string  the javascript code to insert in the link (or in the form)
     */
    function javascriptLinkConfirm($message, $form = false)
    {
        if(!$form) {
            return "onclick=\"javascript:return confirm('".str_replace("'"," ",$message)."')\"";
        } else {
            return "onSubmit=\"javascript:return confirm('".str_replace("'"," ",$message)."')\"";
        }
    }

    /**
     * Set the page's title, meta description and meta keywords
     * Datas are supposed to be sanitized
     *
     * @param  string $pageTitle       Page's Title
     * @param  string $metaDescription Page's meta description
     * @param  string $metaKeywords    Page's meta keywords
     * @return void
     */
    function setMetas($pageTitle = '', $metaDescription = '', $metaKeywords = '')
    {
        global $xoTheme, $xoTheme, $xoopsTpl;
        $xoopsTpl->assign('xoops_pagetitle', $pageTitle);
        if(isset($xoTheme) && is_object($xoTheme)) {
            if(!empty($metaKeywords)) {
                $xoTheme->addMeta( 'meta', 'keywords', $metaKeywords);
            }
            if(!empty($metaDescription)) {
                $xoTheme->addMeta( 'meta', 'description', $metaDescription);
            }
        } elseif(isset($xoopsTpl) && is_object($xoopsTpl)) {    // Compatibility for old Xoops versions
            if(!empty($metaKeywords)) {
                $xoopsTpl->assign('xoops_meta_keywords', $metaKeywords);
            }
            if(!empty($metaDescription)) {
                $xoopsTpl->assign('xoops_meta_description', $metaDescription);
            }
        }
    }

    /**
     * Remove module's cache
     */
    function updateCache()
    {
        global $xoopsModule;
        $folder = $xoopsModule->getVar('dirname');
        $tpllist = array();
        require_once XOOPS_ROOT_PATH.'/class/xoopsblock.php';
        require_once XOOPS_ROOT_PATH.'/class/template.php';
        $tplfile_handler =& xoops_gethandler('tplfile');
        $tpllist = $tplfile_handler->find(null, null, null, $folder);
        xoops_template_clear_module_cache($xoopsModule->getVar('mid'));            // Clear module's blocks cache

        foreach ($tpllist as $onetemplate) {    // Remove cache for each page.
            if( $onetemplate->getVar('tpl_type') == 'module' ) {
                //	Note, I've been testing all the other methods (like the one of Smarty) and none of them run, that's why I have used this code
                $files_del = array();
                $files_del = glob(XOOPS_CACHE_PATH.'/*'.$onetemplate->getVar('tpl_file').'*');
                if(count($files_del) >0 && is_array($files_del)) {
                    foreach($files_del as $one_file) {
                        if(is_file($one_file)) {
                            unlink($one_file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Redirect user with a message
     *
     * @param string $message message to display
     * @param string $url     The place where to go
     * @param integer timeout Time to wait before to redirect
     */
    function redirect($message = '', $url = 'index.php', $time = 2)
    {
        redirect_header($url, $time, $message);
        exit();
    }

    /**
     * Internal function used to get the handler of the current module
     *
     * @return object The module
     */
    protected function _getModule()
    {
        static $mymodule;
        if (!isset($mymodule)) {
            global $xoopsModule;
            if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == BIRTHDAY_DIRNAME ) {
                $mymodule =& $xoopsModule;
            } else {
                $hModule = &xoops_gethandler('module');
                $mymodule = $hModule->getByDirname(BIRTHDAY_DIRNAME);
            }
        }

        return $mymodule;
    }

    /**
     * Returns the module's name (as defined by the user in the module manager) with cache
     * @return string Module's name
     */
    function getModuleName()
    {
        static $moduleName;
        if(!isset($moduleName)) {
            $mymodule = self::_getModule();
            $moduleName = $mymodule->getVar('name');
        }

        return $moduleName;
    }

    /**
     * Create a title for the href tags inside html links
     *
     * @param  string $title Text to use
     * @return string Formated text
     */
    function makeHrefTitle($title)
    {
        $s = "\"'";
        $r = '  ';

        return strtr($title, $s, $r);
    }

    /**
     * Verify that the current user is a member of the Admin group
     *
     * @return booleean Admin or not
    */
    function isAdmin()
    {
        global $xoopsUser, $xoopsModule;
        if(is_object($xoopsUser)) {
            if(in_array(XOOPS_GROUP_ADMIN,$xoopsUser->getGroups())) {
                return true;
            } else {
                if(isset($xoopsModule)) {
                    if($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Returns the current date in the Mysql format
     *
     * @return string Date in the Mysql format
     */
    function getCurrentSQLDate()
    {
        return date('Y-m-d');    // 2007-05-02
    }

    /**
     * Convert a Mysql date to the human's format
     *
     * @param  string $date The date to convert
     * @return string The date in a human form
     */
    function SQLDateToHuman($date, $format='s')
    {
        if($date != '0000-00-00' && xoops_trim($date) != '') {
            return formatTimestamp(strtotime($date), $format);
        } else {
            return  '';
        }
    }

    /**
     * Convert a timestamp to a Mysql date
     *
     * @param  integer $timestamp The timestamp to use
     * @return string  The date in the Mysql format
     */
    function timestampToMysqlDate($timestamp)
    {
        return date('Y-m-d', intval($timestamp));
    }

    /**
     * This function indicates if the current Xoops version needs to add asterisks to required fields in forms
     *
     * @return boolean Yes = we need to add them, false = no
     */
    function needsAsterisk()
    {
        if(self::isX22() || self::isX23()) {
            return false;
        }
        if(strpos(strtolower(XOOPS_VERSION), 'impresscms') !== false) {
            return false;
        }
        if(strpos(strtolower(XOOPS_VERSION), 'legacy') === false) {
            $xv = xoops_trim(str_replace('XOOPS ','',XOOPS_VERSION));
            if(intval(substr($xv,4,2)) >= 17) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark the mandatory fields of a form with a star
     *
     * @param  object $sform    The form to modify
     * @param  string $caracter The character to use to mark fields
     * @return object The modified form
     */
    function &formMarkRequiredFields(&$sform)
    {
        if(self::needsAsterisk()) {
            $tblRequired = array();
            foreach($sform->getRequired() as $item) {
                $tblRequired[] = $item->_name;
            }
            $tblElements = array();
            $tblElements = & $sform->getElements();
            $cnt = count($tblElements);
            for($i=0; $i<$cnt; $i++) {
                if( is_object($tblElements[$i]) && in_array($tblElements[$i]->_name, $tblRequired)
                ) {
                    $tblElements[$i]->_caption .= ' *';
                }
            }
        }

        return $sform;
    }

    /**
     * Create a unique upload filename
     *
     * @param  string  $folder   The folder where the file will be saved
     * @param  string  $fileName Original filename (coming from the user)
     * @param  boolean $trimName Do we need to create a short unique name ?
     * @return string  The unique filename to use (with its extension)
     */
    function createUploadName($folder, $fileName, $trimName=false)
    {
        $workingfolder = $folder;
        if( xoops_substr($workingfolder,strlen($workingfolder)-1,1) != '/' ) {
            $workingfolder .= '/';
        }
        $ext = basename($fileName);
        $ext = explode('.', $ext);
        $ext = '.'.$ext[count($ext)-1];
        $true = true;
        while($true) {
            $ipbits = explode('.', $_SERVER['REMOTE_ADDR']);
            list($usec, $sec) = explode(' ',microtime());
            $usec = (integer) ($usec * 65536);
            $sec = ((integer) $sec) & 0xFFFF;

            if($trimName) {
                $uid = sprintf("%06x%04x%04x",($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
            } else {
                $uid = sprintf("%08x-%04x-%04x",($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
            }
               if(!file_exists($workingfolder.$uid.$ext)) {
                   $true=false;
               }
        }

        return $uid.$ext;
    }

    /**
     * Create the meta keywords based on the content
     *
     * @param  string $content Content from which we have to create metakeywords
     * @return string The list of meta keywords
     */
    function createMetaKeywords($content)
    {
        $keywordscount = 50;
        $keywordsorder = 0;

        $tmp = array();
        // Search for the "Minimum keyword length"
        if(isset($_SESSION['birthday_keywords_limit'])) {
            $limit = $_SESSION['birthday_keywords_limit'];
        } else {
            $config_handler =& xoops_gethandler('config');
            $xoopsConfigSearch =& $config_handler->getConfigsByCat(XOOPS_CONF_SEARCH);
            $limit = $xoopsConfigSearch['keyword_min'];
            $_SESSION['birthday_keywords_limit'] = $limit;
        }
        $myts =& MyTextSanitizer::getInstance();
        $content = str_replace ("<br />", " ", $content);
        $content= $myts->undoHtmlSpecialChars($content);
        $content= strip_tags($content);
        $content=strtolower($content);
        $search_pattern=array("&nbsp;","\t","\r\n","\r","\n",",",".","'",";",":",")","(",'"','?','!','{','}','[',']','<','>','/','+','-','_','\\','*');
        $replace_pattern=array(' ',' ',' ',' ',' ',' ',' ',' ','','','','','','','','','','','','','','','','','','','');
        $content = str_replace($search_pattern, $replace_pattern, $content);
        $keywords = explode(' ',$content);
        switch($keywordsorder) {
            case 0:    // Ordre d'apparition dans le texte
                $keywords = array_unique($keywords);
                break;
            case 1:    // Ordre de fréquence des mots
                $keywords = array_count_values($keywords);
                asort($keywords);
                $keywords = array_keys($keywords);
                break;
            case 2:    // Ordre inverse de la fréquence des mots
                $keywords = array_count_values($keywords);
                arsort($keywords);
                $keywords = array_keys($keywords);
                break;
        }
        // Remove black listed words
        if(xoops_trim(self::getModuleOption('metagen_blacklist')) != '') {
            $metagen_blacklist = str_replace("\r", '', self::getModuleOption('metagen_blacklist'));
            $metablack = explode("\n", $metagen_blacklist);
            array_walk($metablack, 'trim');
            $keywords = array_diff($keywords, $metablack);
        }

        foreach($keywords as $keyword) {
            if(strlen($keyword)>=$limit && !is_numeric($keyword)) {
                $tmp[] = $keyword;
            }
        }
        $tmp = array_slice($tmp, 0, $keywordscount);
        if(count($tmp) > 0) {
            return implode(',',$tmp);
        } else {
            if(!isset($config_handler) || !is_object($config_handler)) {
                $config_handler =& xoops_gethandler('config');
            }
            $xoopsConfigMetaFooter =& $config_handler->getConfigsByCat(XOOPS_CONF_METAFOOTER);
            if(isset($xoopsConfigMetaFooter['meta_keywords'])) {
                return $xoopsConfigMetaFooter['meta_keywords'];
            } else {
                return '';
            }
        }
    }

    /**
     * Fonction chargée de gérer l'upload
     *
     * @param  integer $indice L'indice du fichier à télécharger
     * @return mixed   True si l'upload s'est bien déroulé sinon le message d'erreur correspondant
     */
    function uploadFile($indice, $dstpath = XOOPS_UPLOAD_PATH, $mimeTypes = null, $uploadMaxSize = null)
    {
        require_once XOOPS_ROOT_PATH.'/class/uploader.php';
        global $destname;
        if(isset($_POST['xoops_upload_file'])) {
            require_once XOOPS_ROOT_PATH.'/class/uploader.php';
            $fldname = '';
            $fldname = $_FILES[$_POST['xoops_upload_file'][$indice]];
            $fldname = (get_magic_quotes_gpc()) ? stripslashes($fldname['name']) : $fldname['name'];
            if(xoops_trim($fldname != '')) {
                $destname = self::createUploadName($dstpath ,$fldname, true);
                if($mimeTypes === null) {
                    $permittedtypes = explode("\n",str_replace("\r",'', self::getModuleOption('mimetypes')));
                    array_walk($permittedtypes, 'trim');
                } else {
                    $permittedtypes = $mimeTypes;
                }
                if($uploadMaxSize === null) {
                    $uploadSize = self::getModuleOption('maxuploadsize');
                } else {
                    $uploadSize = $uploadMaxSize;
                }
                $uploader = new XoopsMediaUploader($dstpath, $permittedtypes, $uploadSize);
                //$uploader->allowUnknownTypes = true;
                $uploader->setTargetFileName($destname);
                if ($uploader->fetchMedia($_POST['xoops_upload_file'][$indice])) {
                    if ($uploader->upload()) {
                        return true;
                    } else {
                        return _ERRORS.' '.htmlentities($uploader->getErrors());
                    }
                } else {
                    return htmlentities($uploader->getErrors());
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Resize a Picture to some given dimensions
     *
     * @author GIJOE
     * @param string  $src_path     Picture's source
     * @param string  $dst_path     Picture's destination
     * @param integer $param_width  Maximum picture's width
     * @param integer $param_height Maximum picture's height
     */
    function resizePicture( $src_path , $dst_path, $param_width , $param_height, $keep_original = false)
    {
        if( ! is_readable( $src_path ) ) {
            return 0 ;
        }

        list( $width , $height , $type ) = getimagesize( $src_path ) ;
        switch( $type ) {
            case 1 : // GIF
                if(!$keep_original) {
                    @rename( $src_path, $dst_path ) ;
                } else {
                    @copy( $src_path, $dst_path ) ;
                }

                return 2 ;
            case 2 : // JPEG
                $src_img = imagecreatefromjpeg( $src_path ) ;
                break ;
            case 3 : // PNG
                $src_img = imagecreatefrompng( $src_path ) ;
                break ;
            default :
                if(!$keep_original) {
                    @rename( $src_path, $dst_path ) ;
                } else {
                    @copy( $src_path, $dst_path ) ;
                }

                return 2 ;
        }

        if($param_width > 0 && $param_height > 0) {
            if( $width > $param_width || $height > $param_height ) {
                if( $width / $param_width > $height / $param_height ) {
                    $new_w = $param_width ;
                    $scale = $width / $new_w ;
                    $new_h = intval( round( $height / $scale ) ) ;
                } else {
                    $new_h = $param_height ;
                    $scale = $height / $new_h ;
                    $new_w = intval( round( $width / $scale ) ) ;
                }
                $dst_img = imagecreatetruecolor( $new_w , $new_h ) ;
                imagecopyresampled( $dst_img , $src_img , 0 , 0 , 0 , 0 , $new_w , $new_h , $width , $height ) ;
            }
        }

        if( isset( $dst_img ) && is_resource( $dst_img ) ) {
            switch( $type ) {
                case 2 : // JPEG
                    imagejpeg( $dst_img , $dst_path ) ;
                    imagedestroy( $dst_img ) ;
                    break ;
                case 3 : // PNG
                    imagepng( $dst_img , $dst_path ) ;
                    imagedestroy( $dst_img ) ;
                    break ;
            }
        }

        imagedestroy( $src_img );
        if( ! is_readable( $dst_path ) ) {
            if(!$keep_original) {
                @rename( $src_path , $dst_path ) ;
            } else {
                @copy( $src_path , $dst_path ) ;
            }

            return 3 ;
        } else {
            if(!$keep_original) {
                @unlink( $src_path ) ;
            }

            return 1 ;
        }
    }

    function close_tags($string)
    {
        // match opened tags
        if(preg_match_all('/<([a-z\:\-]+)[^\/]>/', $string, $start_tags)) {
            $start_tags = $start_tags[1];

            // match closed tags
            if(preg_match_all('/<\/([a-z]+)>/', $string, $end_tags)) {
                $complete_tags = array();
                  $end_tags = $end_tags[1];

                  foreach($start_tags as $key => $val) {
                    $posb = array_search($val, $end_tags);
                    if(is_integer($posb)) {
                          unset($end_tags[$posb]);
                    } else {
                        $complete_tags[] = $val;
                    }
                }
            } else {
                $complete_tags = $start_tags;
            }

            $complete_tags = array_reverse($complete_tags);
            for($i = 0; $i < count($complete_tags); $i++) {
                $string .= '</' . $complete_tags[$i] . '>';
            }
        }

        return $string;
    }

    function truncate_tagsafe($string, $length = 80, $etc = '...', $break_words = false)
    {
        if ($length == 0) return '';

        if (strlen($string) > $length) {
            $length -= strlen($etc);
            if (!$break_words) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
                $string = preg_replace('/<[^>]*$/', '', $string);
                $string = self::close_tags($string);
            }

            return $string . $etc;
        } else {
            return $string;
        }
    }

    /**
     * Create an infotip
     */
    function makeInfotips($text)
    {
        $infotips = self::getModuleOption('infotips');
        if($infotips > 0) {
            $myts =& MyTextSanitizer::getInstance();

            return $myts->htmlSpecialChars(xoops_substr(strip_tags($text),0,$infotips));
        }

        return '';
    }

    /**
     * Retourne un breadcrumb en fonction des paramètres passés et en partant (d'office) de la racine du module
     *
     * @param  array  $path  Le chemin complet (excepté la racine) du breadcrumb sous la forme clé=url valeur=titre
     * @param  string $raquo Le séparateur par défaut à utiliser
     * @return string le breadcrumb
     */
    function breadcrumb($path, $raquo = ' &raquo; ')
    {
        $breadcrumb = '';
        $workingBreadcrumb = array();
        if(is_array($path))    {
            $moduleName = self::getModuleName();
            $workingBreadcrumb[] = "<a href='".BIRTHDAY_URL."' title='".self::makeHrefTitle($moduleName)."'>".$moduleName.'</a>';
            foreach($path as $url=>$title) {
                $workingBreadcrumb[] = "<a href='".$url."'>".$title.'</a>';
            }
            $cnt = count($workingBreadcrumb);
            for($i=0; $i<$cnt; $i++) {
                if($i == $cnt-1) {
                    $workingBreadcrumb[$i] = strip_tags($workingBreadcrumb[$i]);
                }
            }
            $breadcrumb = implode($raquo, $workingBreadcrumb);
        }

        return $breadcrumb;
    }
}
