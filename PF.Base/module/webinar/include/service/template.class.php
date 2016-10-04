<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		CodeMake.Org
 * @package 		Module_Webinar
 */
class Webinar_Service_Template extends Phpfox_Service
{
    public $aPhotoSizes = array(55, 75, 120, 155);

    public function __construct()
    {
        Phpfox::getLib('setting')->setParam('webinar.module_image_url', Phpfox::getParam('core.path') . 'module/webinar/static/image/default/default/');
        Phpfox::getLib('setting')->setParam('webinar.module_image_dir', PHPFOX_DIR . 'module'.PHPFOX_DS.'webinar'.PHPFOX_DS.'static'.PHPFOX_DS.'image'.PHPFOX_DS.'default'.PHPFOX_DS.'default'.PHPFOX_DS);
        Phpfox::getLib('setting')->setParam('webinar.image_dir', Phpfox::getParam('core.dir_pic') . 'webinar'.PHPFOX_DS);
        Phpfox::getLib('setting')->setParam('webinar.image_url', Phpfox::getParam('core.url_pic') . 'webinar/');
    }

    public function image($sPath = '', $sImage = '', $sSize = '', $aParams = array(), $bUrl = false){

        $sDestinationDir = '';
        $sDestinationUrl = '';

        $sHtmlImage = '';
        if (!empty($sPath)){
            $sPathUrl = str_replace('dir', 'url', $sPath);
            $sPathDir = str_replace('url', 'dir', $sPath);
            if (Phpfox::getParam($sPathDir) && Phpfox::getParam($sPathUrl)){
                $sDestinationUrl .= Phpfox::getParam($sPathUrl);
                $sDestinationDir .= Phpfox::getParam($sPathDir);
            }
        }

        if (strpos($sImage, '%s')){			
            $sDestinationUrl .= sprintf($sImage, $sSize);
            $sDestinationDir .= str_replace(' ', '', preg_replace('#/#', PHPFOX_DS, sprintf($sImage, $sSize)));			
        }else{
            $sDestinationUrl .= $sImage;
            $sDestinationDir .= str_replace(' ', '', preg_replace('#/#', PHPFOX_DS, $sImage));
        }
		
        if (!file_exists($sDestinationDir) || empty($sImage)){
            $sDestinationUrl = file_exists(Phpfox::getParam('webinar.module_image_dir').str_replace(' ', '', preg_replace('#/#', PHPFOX_DS, sprintf("noPhoto%s.png", $sSize))))?Phpfox::getParam('webinar.module_image_url').sprintf("noPhoto%s.png", $sSize):Phpfox::getParam('webinar.module_image_url')."noPhoto.png";
        }		
        if (!$bUrl){
            $sHtmlImage .= '<img src="'.$sDestinationUrl.'" ';
            foreach($aParams as $sKey=>$sParam){
                $sHtmlImage .= sprintf($sKey.' ="%s" ', $sParam);
            }
            $sHtmlImage .= '/>';
            echo($sHtmlImage);
        }else{

            return $sDestinationUrl;
        }
    }


}

?>