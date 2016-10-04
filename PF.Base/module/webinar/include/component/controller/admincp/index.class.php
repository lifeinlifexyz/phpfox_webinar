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

class Webinar_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if ($aOrder = $this->request()->getArray('order'))
		{
			if (Phpfox::getService('webinar.category.process')->updateOrder($aOrder))
			{
				$this->url()->send('admincp.webinar', null, Phpfox::getPhrase('webinar.category_order_successfully_updated'));
			}
		}		
		
		if ($iDelete = $this->request()->getInt('delete'))
		{
			if (Phpfox::getService('webinar.category.process')->delete($iDelete))
			{
				$this->url()->send('admincp.webinar', null, Phpfox::getPhrase('webinar.category_successfully_deleted'));
			}
		}
	
		$this->template()->setTitle(Phpfox::getPhrase('webinar.manage_categories'))
			->setBreadcrumb(Phpfox::getPhrase('webinar.manage_categories'), $this->url()->makeUrl('admincp.webinar'))
			->setPhrase(array('webinar.are_you_sure_this_will_delete_all_webinars_that_belong_to_this_category_and_cannot_be_undone'))
			->setHeader(array(
					'jquery/ui.js' => 'static_script',
					'admin.js' => 'module_webinar',
					'<script type="text/javascript">$Behavior.admincpEditwebinar = function() { $Core.webinar.url(\'' . $this->url()->makeUrl('admincp.webinar') . '\'); }</script>'
				)
			)
			->assign(array(
					'sCategories' => Phpfox::getService('webinar.category')->display('admincp')->get()
				)
			);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('webinar.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>