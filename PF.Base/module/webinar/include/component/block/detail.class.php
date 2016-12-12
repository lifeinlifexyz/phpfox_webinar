<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        CodeMake.Org
 * @package        Module_Webinar
 */

class Webinar_Component_Block_Detail extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!$iId = Phpfox::getLib('request')->get('req3')){
             return false;
        }

        $aWebinarDetail = Phpfox::getService('webinar.webinar')->getWebinarDetail($iId);

        if (!isset($aWebinarDetail['webinar_id'])){
            return false;
        }
        $aWebinarDetail['category_url'] = $this->url()->permalink(array('webinar.category', 'view' => $this->request()->get('view')), isset($aWebinarDetail['category_id'])?$aWebinarDetail['category_id']:0, isset($aWebinarDetail['category_name'])?$aWebinarDetail['category_name']:'');
		$this->template()->assign(array(
				'sHeader' => _p('webinar.webinar_details'),
				'aWebinarDetail' => $aWebinarDetail,
				'iWebinarView' => $this->request()->getInt('req3')
			)
		);	

		(($sPlugin = Phpfox_Plugin::get('webinar.component_block_detail_process')) ? eval($sPlugin) : false);
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_block_categories_clean')) ? eval($sPlugin) : false);
	}	
}

?>