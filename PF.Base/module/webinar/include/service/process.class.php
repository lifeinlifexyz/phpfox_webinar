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

class Webinar_Service_Process extends Phpfox_Service
{
    private $_oWebinarTemplate, $_sWebinarImageFolder, $_aCategories;
    private $_aOSList = array
    (
        'Windows 3.11' => 'Win16',
        'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
        'Windows 98' => '(Windows 98)|(Win98)',
        'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
        'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
        'Windows Server 2003' => '(Windows NT 5.2)',
        'Windows Vista' => '(Windows NT 6.0)',
        'Windows 7' => '(Windows NT 7.0)',
        'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
        'Windows ME' => 'Windows ME',
        'Open BSD' => 'OpenBSD',
        'Sun OS' => 'SunOS',
        'Linux' => '(Linux)|(X11)',
        'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
        'QNX' => 'QNX',
        'BeOS' => 'BeOS',
        'OS/2' => 'OS/2',
        'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
    );
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('webinar');
        $this->_oWebinarTemplate = Phpfox::getService('webinar.template');
        $this->_sWebinarImageFolder = Phpfox::getParam('webinar.image_dir');
        ini_set('session.gc_maxlifetime', '10800');
        ini_set('max_input_time', '10800');
        ini_set('max_execution_time', '10800');
        ini_set('upload_max_filesize', Phpfox::getUserParam('webinar.upload_max_filesize').'M');
        ini_set('post_max_size', Phpfox::getUserParam('webinar.upload_max_filesize').'M');
    }

    public function add($aVals)
    {
        Phpfox::isUser(true);
        if (!empty($_FILES['image']['name'])) {
            $aImage = Phpfox::getLib('file')->load('image', array(
                'jpg',
                'gif',
                'png'
            ),Phpfox::getUserParam('webinar.webinar_max_upload_icon_size'));
        }
        if (isset($aVals['category']) && count($aVals['category']))
        {
            foreach ($aVals['category'] as $iCategory)
            {
                if (empty($iCategory))
                {
                    continue;
                }

                if (!is_numeric($iCategory))
                {
                    continue;
                }

                $this->_aCategories[] = $iCategory;
            }
        }

        $oFilter = Phpfox::getLib('parse.input');

        $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0, $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
        $iId = $this->database()->insert($this->_sTable, array(
                'title' => $oFilter->prepare(strip_tags($aVals['title'])),
                'description' => $aVals['description'],
                'is_search' => empty($aVals['is_search']) ? 0 : 1,
                'is_closed' => !empty($aVals['is_closed']) ? 1 : 0,
                'is_commented' => !empty($aVals['is_commented']) ? 1 : 0,
                'user_id' => Phpfox::getUserId(),
                'link_to_source' => $aVals['link_to_source'],
                'time_stamp' => PHPFOX_TIME,
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                'start_time' => $iStartTime
            )
        );

        if (!$iId) {
            return false;
        }
        if (isset($aVals['chooseSubscribers'])){
            $this->inviteMembersVia($iId, $aVals['chooseSubscribers'], false, $aVals['skip_send_invitation']);
        }

        if (isset($aImage)) {
            $this->isThereWebinarImageFolder();
            $oImage = Phpfox::getLib('image');
            $sFileName = Phpfox::getLib('file')->upload('image', $this->_sWebinarImageFolder, $iId);
            $iFileSizes = filesize($this->_sWebinarImageFolder . sprintf($sFileName, ''));

            foreach ($this->_oWebinarTemplate->aPhotoSizes as $iSize) {
                $oImage->createThumbnail($this->_sWebinarImageFolder . sprintf($sFileName, ''), Phpfox::getParam('webinar.image_dir') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
                $oImage->createThumbnail($this->_sWebinarImageFolder . sprintf($sFileName, ''), Phpfox::getParam('webinar.image_dir') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
                $iFileSizes += filesize($this->_sWebinarImageFolder . sprintf($sFileName, '_' . $iSize));
            }

            $this->database()->update($this->_sTable, array('image_src' => $sFileName), 'webinar_id = ' . $iId);
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', $iFileSizes);
        }
        if (isset($this->_aCategories) && count($this->_aCategories))
        {
            foreach ($this->_aCategories as $iCategoryId)
            {
                $this->database()->insert(Phpfox::getT('webinar_category_data'), array('webinar_id' => $iId, 'category_id' => $iCategoryId));
            }
        }
        return $iId;
    }

    public function update($iId, $aVals)
    {
        Phpfox::isUser(true);
        $this->database()->where('webinar_id = ' . $iId);
        $aWebinar = $this->database()->select('*')
            ->from($this->_sTable)
            ->execute('getSlaveRow');
        if (!isset($aWebinar['webinar_id'])) {
            return false;
        }
        $oFilter = Phpfox::getLib('parse.input');
        $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0, $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
        $bIsUpdated = $this->database()->update($this->_sTable, array(
                'title' => $oFilter->prepare(strip_tags($aVals['title'])),
                'description' => $aVals['description'],
                'is_search' => empty($aVals['is_search']) ? 0 : 1,
                'is_closed' => !empty($aVals['is_closed']) ? 1 : 0,
                'is_commented' => !empty($aVals['is_commented']) ? 1 : 0,
                'user_id' => Phpfox::getUserId(),
                'time_stamp' => PHPFOX_TIME,
                'link_to_source' => $aVals['link_to_source'],
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                'start_time' => $iStartTime,
            ), 'webinar_id = ' . $aWebinar['webinar_id'], true
        );
        if (isset($aVals['category']) && count($aVals['category']))
        {
            foreach ($aVals['category'] as $iCategory)
            {
                if (empty($iCategory))
                {
                    continue;
                }

                if (!is_numeric($iCategory))
                {
                    continue;
                }

                $this->_aCategories[] = $iCategory;
            }
        }
        $this->database()->delete(Phpfox::getT('webinar_category_data'), 'webinar_id = ' . (int) $iId);
        if (isset($this->_aCategories) && count($this->_aCategories))
        {
            foreach ($this->_aCategories as $iCategoryId)
            {
                $this->database()->insert(Phpfox::getT('webinar_category_data'), array('webinar_id' => $iId, 'category_id' => $iCategoryId));
            }
        }
        if (!$bIsUpdated) {
            return false;
        }

        if (!empty($_FILES['image']['name'])) {
            $aImage = Phpfox::getLib('file')->load('image', array(
                'jpg',
                'gif',
                'png'
            ),Phpfox::getUserParam('webinar.webinar_max_upload_icon_size'));
        }
        if (isset($aImage)) {
            $this->isThereWebinarImageFolder();
            $oImage = Phpfox::getLib('image');
            $sFileName = Phpfox::getLib('file')->upload('image', $this->_sWebinarImageFolder, $iId);
            $iFileSizes = filesize($this->_sWebinarImageFolder . sprintf($sFileName, ''));

            foreach ($this->_oWebinarTemplate->aPhotoSizes as $iSize) {
                $oImage->createThumbnail($this->_sWebinarImageFolder . sprintf($sFileName, ''), Phpfox::getParam('webinar.image_dir') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
                $oImage->createThumbnail($this->_sWebinarImageFolder . sprintf($sFileName, ''), Phpfox::getParam('webinar.image_dir') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
                $iFileSizes += filesize($this->_sWebinarImageFolder . sprintf($sFileName, '_' . $iSize));
            }

            $this->database()->update($this->_sTable, array('image_src' => $sFileName), 'webinar_id = ' . $iId);
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', $iFileSizes);
        }

        if (isset($aVals['chooseSubscribers'])){
            $this->inviteMembersVia($iId, $aVals['chooseSubscribers'], false, $aVals['skip_send_invitation']);
        }

        return $iId;
    }

    public function delete($iId)
    {
        Phpfox::isUser();
        $this->database()->where('webinar_id = ' . $iId);
        $aWebinar = $this->database()->select('*')
            ->from($this->_sTable)
            ->execute('getSlaveRow');

        if (!isset($aWebinar['webinar_id'])) {
            return false;
        }
        if ($this->database()->delete($this->_sTable, 'webinar_id = ' . (int)$aWebinar['webinar_id'])) {
            $this->database()->delete(Phpfox::getT('webinar_subscriber'), 'webinar_id = ' . (int)$aWebinar['webinar_id']);
            return true;
        }

        return false;
    }

    public function inviteMembersVia($iId, $aMembers, $isAjax=false, $bSkipSendInvite=0)
    {
        $aWebinar = Phpfox::getService('webinar.webinar')->getForEdit($iId);
        if (!isset($aWebinar['webinar_id'])){
            return false;
        }
        if (!$isAjax){
            $this->database()->delete(Phpfox::getT("webinar_subscriber"), sprintf("webinar_id = %s", $aWebinar['webinar_id']));
        }
        $aSendToUsers = array();
        if (!empty($aMembers)) {

            foreach ($aMembers as $iMember) {
                if (!Phpfox::getService('webinar.webinar')->isSubscriberJoined($aWebinar['webinar_id'], (int)$iMember)) {
                    if ($this->database()->query(sprintf("INSERT INTO %s(webinar_id, user_id) VALUE('%s', '%s')", Phpfox::getT('webinar_subscriber'), $iId, $iMember))) {
                        $aSendToUsers[] = $iMember;
                    }
                }
            }

            if ($isAjax == false && $bSkipSendInvite == 0) {
                if (Phpfox::getParam('webinar.via_send') == trim('Message') || strpos(Phpfox::getParam('webinar.via_send'), 'Message')) {
                    if (!empty($aSendToUsers)) {

                        $sMessage = Phpfox::getPhrase('webinar.full_name_invites_you_to_a_webinar_named_title', array(
                                'full_name' => Phpfox::getUserBy('full_name'),
                                'title' => $aWebinar['title'],
                                'message' => Phpfox::getLib('parse.input')->prepare($aWebinar['description']),
                                'time' => $aWebinar['start_month'] . '.' . $aWebinar['start_day'] . '.' . $aWebinar['start_year'] . ' ' . Phpfox::getPhrase('webinar.time_separator') . ' ' . $aWebinar['start_hour'] . ':' . $aWebinar['start_minute'],
                                'link' => Phpfox::getLib('url')->permalink('webinar.view', $aWebinar['webinar_id'], $aWebinar['title'])
                            )
                        );
                        $aMessage = array(
                            'to' => $aSendToUsers,
                            'subject' => Phpfox::getPhrase('webinar.module_webinar') . ': ' . $aWebinar['title'],
                            'message' => $sMessage
                        );
                        $bMailSend = Phpfox::getService('mail.process')->add($aMessage);
                        if ($isAjax && $bMailSend) {
                            Phpfox::getLib('ajax')->alert(Phpfox::getPhrase('webinar.the_invitation_has_been_sent_successfully'));
                            //Phpfox::getLib('ajax')->call("$('#alert_message #public_message').text('".Phpfox::getPhrase('webinar.the_invitation_has_been_sent_successfully')."').fadeIn(100);");
                            //Phpfox::getLib('ajax')->call("$('#alert_message #public_message').fadeOut(5000);");
                        }
                        return $bMailSend;
                    }


                } else {
                    if (!empty($aSendToUsers)) {
                        foreach ($aSendToUsers as $iUser) {
                            Phpfox::getService('notification.process')->add('webinar', $aWebinar['webinar_id'], $iUser, Phpfox::getUserId());
                        }
                        if ($isAjax) {
                            Phpfox::getLib('ajax')->alert(Phpfox::getPhrase('webinar.the_invitation_has_been_sent_successfully'));
                            //Phpfox::getLib('ajax')->call("$('#alert_message #public_message').text('".Phpfox::getPhrase('webinar.the_invitation_has_been_sent_successfully')."').fadeIn(100);");
                            //Phpfox::getLib('ajax')->call("$('#alert_message #public_message').fadeOut(5000);");
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function isThereWebinarImageFolder()
    {
        $sRootImageDirectory = rtrim(Phpfox::getParam('webinar.image_dir'), "\ ");
        if (!is_dir($sRootImageDirectory)) {
            return Phpfox::getLib('file')->mkdir($sRootImageDirectory, true, 0777);
        }
    }

    public function updateCounterComment($iId)
    {
        $iCommentCount = $this->database()->select("COUNT(*)")
            ->from(Phpfox::getT('webinar_comment'))
            ->where('webinar_id = ' . $iId)
            ->execute('getField');
        return $this->database()->update(Phpfox::getT('webinar'), array('total_comment' => (int)$iCommentCount), 'webinar_id = ' . $iId);
    }

    public function getActiveSubscribers($iId){
        $this->database()->clean();
        if (empty($iId)){
            return false;
        }
        $aRows = $this->database()->select('DISTINCT' . Phpfox::getUserField() . ', ws.*, ls.user_id AS is_online')
            ->from(Phpfox::getT('log_session'), 'ls')
            ->leftJoin(Phpfox::getT('webinar_subscriber'), 'ws', 'ws.user_id = ls.user_id')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ws.user_id')
            ->where('ws.is_banned = 0 AND ws.webinar_id = '.(int)$iId)
            ->execute('getRows');
        foreach ($aRows as $iKey=>$aRow){
            $aRows[$iKey]['profile_url'] = Phpfox::getLib('url')->makeUrl($aRow['user_name']);
        }
        return $aRows;
    }

    public function getAllActiveMembersOnWebinar($iId){
        $this->database()->clean();
        $aWebinar = Phpfox::getService('webinar.webinar')->getForProcess($iId);

        if (!isset($aWebinar['webinar_id'])){
            return false;
        }
        $aPublisher = Phpfox::getService('user')->get($aWebinar['user_id']);
        $aPublisher['profile_url'] = Phpfox::getLib('url')->makeUrl($aPublisher['user_name']);
        $aMembers = array();
        $aMembers['publisher'] = $aPublisher;
        $aMembers['subscribers'] = $this->getActiveSubscribers($iId);
        return array($aWebinar, $aMembers);
    }

    public function isPublisher($iId){
        if (empty($iId)){
            return false;
        }
        if ($this->database()->select('COUNT(*)')->from($this->_sTable)->where('webinar_id='.$iId.' AND user_id = '.Phpfox::getUserId())->execute('getField')){
            return true;
        }
        return false;
    }
    public function deleteUserFromWebinar($iId, $iUserId){
        Phpfox::isUser();
        $this->database()->where('webinar_id = ' . $iId. ' AND user_id = '.$iUserId);
        $aSubscriber = $this->database()->select('*')
            ->from(Phpfox::getT('webinar_subscriber'))
            ->execute('getSlaveRow');

        if (!isset($aSubscriber['webinar_id'])) {
            return false;
        }
        if ($this->database()->delete(Phpfox::getT('webinar_subscriber'), 'webinar_id = ' . (int)$aSubscriber['webinar_id'].' AND user_id = ' . (int)$aSubscriber['user_id'])) {
            return true;
        }

        return false;
    }

	public function removeWebinarLogo($iId){
		$aWebinar = Phpfox::getService('webinar.webinar')->getForEdit($iId);	
		if (empty($aWebinar['webinar_id'])){
			return false;
		}				
		if ($this->database()->update($this->_sTable, array('image_src' => ''), 'webinar_id = ' . $aWebinar['webinar_id'])){
			return true;
		}		
		return false;
	}
    public function subscriberBan($iId, $iUserId){
        if (empty($iId) || empty($iUserId)){
            return false;
        }
        if ($this->database()->update(Phpfox::getT('webinar_subscriber'), array('is_banned' => '1'), 'webinar_id = ' . $iId . ' AND user_id = '.$iUserId)){
            return true;
        }
        return false;
    }
    public function isSubscriberBan($iId, $iUserId){

        $this->database()->where('webinar_id = ' . $iId. ' AND user_id = '.$iUserId. " AND is_banned = 1");
        return $this->database()->select('*')
            ->from(Phpfox::getT('webinar_subscriber'))
            ->execute('getSlaveRow');
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
        if ($sPlugin = Phpfox_Plugin::get('webinar.service_process__call')) {
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