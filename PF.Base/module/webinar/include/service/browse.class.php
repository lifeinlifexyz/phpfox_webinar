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

class Webinar_Service_Browse extends Phpfox_Service
{
	private $_sCategory = null;

	public function __construct()
	{	
		
	}
	
	public function category($sCategory)
	{
		$this->_sCategory = $sCategory;
		
		return $this;
	}
	
	public function query()
	{
		$this->database()
			->group('w.webinar_id');

		if (Phpfox::isModule('like'))
		{
		    $this->database()->select('l.like_id as is_liked, ')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = "webinar" AND l.item_id = w.webinar_id AND l.user_id = ' . Phpfox::getUserId() . '');
		}

	}	
	
	public function processRows(&$aRows)
	{
		$oReq = Phpfox::getLib('request');
		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['link'] = Phpfox::permalink('webinar.view', $aRow['webinar_id'], $aRow['title']);
			if ((Phpfox::getUserId() && defined('PHPFOX_IS_USER_PROFILE'))
					|| $oReq->get('req1') == 'webinar' && $oReq->get('view') == 'my'
				)
			{
				$aRows[$iKey]['link'] .= 'userid_' . $aRow['user_id'] . '/';
			}
		}
	}	
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{
//        if (Phpfox::isUser() && Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
//		{
//			$this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = w.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
//		}
        $this->database()
			->leftJoin(Phpfox::getT('webinar_category_data'), 'wcd', 'wcd.webinar_id = w.webinar_id')
			->leftJoin(Phpfox::getT('webinar_category'), 'wc', 'wc.category_id = wcd.category_id');
        $this->database()
            ->leftJoin(Phpfox::getT('webinar_subscriber'), 'ws', 'w.webinar_id = ws.webinar_id');
	}		
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('webinar.service_browse__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>
