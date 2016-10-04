<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright     [PHPFOX_COPYRIGHT]
 * @author        CodeMake.Org
 * @package       Module_Webinar
 */
class Webinar_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('webinar');
	}

	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('webinar_id, title, user_id')
			->from(Phpfox::getT('webinar'))
			->where('webinar_id = ' . (int) $iItemId)
			->execute('getSlaveRow');

		if (!isset($aRow['webinar_id']))
		{
			return false;
		}

		$this->database()->updateCount('like', 'type_id = \'webinar\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'webinar', 'webinar_id = ' . (int) $iItemId);

		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('webinar.view', $aRow['webinar_id'], $aRow['title']);

			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('webinar.full_name_liked_your_webinar_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(array('webinar.full_name_liked_your_webinar_title_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->notification('like.new_like')
				->send();

			Phpfox::getService('notification.process')->add('webinar_like', $aRow['webinar_id'], $aRow['user_id']);
		}
	}

	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('w.webinar_id, w.title, w.user_id, u.gender, u.full_name')
			->from(Phpfox::getT('webinar'), 'w')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = w.user_id')
			->where('w.webinar_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getPhrase('webinar.users_liked_gender_own_webinar_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())
		{
			$sPhrase = Phpfox::getPhrase('webinar.users_liked_your_webinar_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else
		{
			$sPhrase = Phpfox::getPhrase('webinar.users_liked_span_class_drop_data_user_row_full_name_s_span_webinar_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}

		return array(
			'link' => Phpfox::getLib('url')->permalink('webinar.view', $aRow['webinar_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'webinar')
		);
	}

	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'webinar\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'webinar', 'webinar_id = ' . (int) $iItemId);
	}

	public function deleteComment($iId)
	{
		$this->database()->update(Phpfox::getT('webinar'), array('total_comment' => array('= total_comment -', 1)), 'webinar_id = ' . (int) $iId);
	}

	public function getAjaxCommentVar()
	{
		return null;
	}

	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
		$aRow = $this->database()->select('w.webinar_id, w.title, u.full_name, u.user_id, u.user_name, u.gender')
			->from(Phpfox::getT('webinar'), 'w')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = w.user_id')
			->where('w.webinar_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');

		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('webinar', 'total_comment', 'webinar_id', $aRow['webinar_id']);
		}

		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('webinar.view', $aRow['webinar_id'], $aRow['title']);

		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['webinar_id'],
				'owner_subject' => Phpfox::getPhrase('webinar.full_name_commented_on_your_webinar_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $this->preParse()->clean($aRow['title'], 100))),
				'owner_message' => Phpfox::getPhrase('webinar.full_name_commented_on_your_webinar_a_href_link_title_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_webinar',
				'mass_id' => 'webinar',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getPhrase('webinar.full_name_commented_on_gender_webinar', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) : Phpfox::getPhrase('webinar.full_name_commented_on_row_full_name_s_webinar', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getPhrase('webinar.full_name_commented_on_gender_webinar_a_href_link_title_a', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'link' => $sLink, 'title' => $aRow['title'])) : Phpfox::getPhrase('webinar.full_name_commented_on_row_full_name_s_webinar_a_href_link_title_a_message', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name'], 'link' => $sLink, 'title' => $aRow['title'])))
			)
		);
	}

	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('webinar_id AS comment_item_id, is_commented AS privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('webinar'))
			->where('webinar_id = ' . (int) $iId)
			->execute('getSlaveRow');

		$aRow['comment_view_id'] = '0';
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], empty($aRow['privacy_comment'])?3:1))
		{
			Phpfox_Error::set(Phpfox::getPhrase('webinar.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

			unset($aRow['comment_item_id']);
		}

		return $aRow;
	}

	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('w.webinar_id, w.title, u.user_id, u.gender, u.user_name, u.full_name')
			->from(Phpfox::getT('webinar'), 'w')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = w.user_id')
			->where('w.webinar_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getPhrase('webinar.users_commented_on_gender_webinar_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())
		{
			$sPhrase = Phpfox::getPhrase('webinar.users_commented_on_your_webinar_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else
		{
			$sPhrase = Phpfox::getPhrase('webinar.users_commented_on_span_class_drop_data_user_row_full_name_s_span_webinar_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' =>  $sTitle));
		}

		return array(
			'link' => Phpfox::getLib('url')->permalink('webinar.view', $aRow['webinar_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'webinar')
		);
	}

    public function getNotification($aNotification){

        $aWebinar = $this->database()->select('w.*')
            ->from(Phpfox::getT('webinar'), 'w')
            ->where('w.webinar_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        $aWebinar['start_day'] = date('d', $aWebinar['start_time']);
        $aWebinar['start_month'] = date('m', $aWebinar['start_time']);
        $aWebinar['start_year'] = date('Y', $aWebinar['start_time']);
        $aWebinar['start_hour'] = date('H', $aWebinar['start_time']);
        $aWebinar['start_minute'] = date('i', $aWebinar['start_time']);

        $aUser = Phpfox::getService('user')->getUser($aWebinar['user_id']);
		$sMessage = Phpfox::getPhrase('webinar.full_name_invites_you_to_a_webinar_named_title_on_time', array(
				'full_name'=>$aUser['full_name'],
				'title'=>$aWebinar['title'],
				'time'=>$aWebinar['start_month'].'.'.$aWebinar['start_day'].'.'.$aWebinar['start_year'].' '.Phpfox::getPhrase('webinar.time_separator').' '.$aWebinar['start_hour'].':'.$aWebinar['start_minute'],
			)
		);

        return array(
            'link' => Phpfox::getLib('url')->permalink('webinar.view', $aWebinar['webinar_id'], $aWebinar['title']),
            'message' => $sMessage
//            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'webinar')
        );
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
		if ($sPlugin = Phpfox_Plugin::get('webinar.service_callback__call'))
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