<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Block that displays a webinar comments on the site depending on
 * where it is placed.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		CodeMake.Org
 * @package  		Module_Webinar
 */
class Webinar_Component_Block_Comment_Display extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iWebinarId = Phpfox::getLib('request')->get('req3');
		$aWebinar = Phpfox::getService('webinar.webinar')->getForProcess($iWebinarId);
		$bIsDisabled = false;

		if (isset($aWebinar['webinar_id']) && (int)$aWebinar['is_commented'] == 0){
			$bIsDisabled = true;
		}

		$aMessages = Phpfox::getService('webinar.comment')->getMessages($iWebinarId, 5);

		// Assign the vars to the template
		$this->template()->assign(array(
				'sHeader' => Phpfox::getPhrase('webinar.comments'),
				'aComments' => $aMessages,
				'iCommentRefresh' => Phpfox::getParam('webinar.comment_ajax_time_refresh')*1000,
				'iCommentWordWrap' => Phpfox::getParam('webinar.comment_wordwrap'),
				'iWebinarId' => $iWebinarId,
				'bIsDisabled' => $bIsDisabled
			)
		);

		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('webinar.component_controller_index_clean')) ? eval($sPlugin) : false);
		
		// Remove template vars from memory
		$this->template()->clean(array(
				'aComments',
				'iShoutboxRefresh',
				'iShoutoutWordWrap'
			)
		);
	}	
}

?>