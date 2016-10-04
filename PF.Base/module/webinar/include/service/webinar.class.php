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

class Webinar_Service_Webinar extends Phpfox_Service{

    private $_aWebinar = array();
    public function __construct(){
		$this->_sTable = Phpfox::getT('webinar');
	}

    public function getForEdit($iId)
    {
        if (!isset($this->_aWebinar[$iId])){
            $aWebinar = $this->database()->select('w.*, wc.name AS category_name, wcd.category_id, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'w')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = w.user_id')
                ->leftJoin(Phpfox::getT('webinar_category_data'), 'wcd', 'wcd.webinar_id = w.webinar_id')
                ->leftJoin(Phpfox::getT('webinar_category'), 'wc', 'wc.category_id = wcd.category_id')
                ->where('w.webinar_id = ' . (int) $iId)
                ->execute('getSlaveRow');

            if (!isset($aWebinar['webinar_id'])){
                return false;
            }

            $aSubscribers = $this->database()->select('ws.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('webinar_subscriber'), 'ws')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ws.user_id')
                ->where('ws.webinar_id = ' . (int) $aWebinar['webinar_id'])
                ->execute('getSlaveRows');
            $aWebinar['aSubscribers'] = $aSubscribers;
            $aWebinar['start_minute'] = date('i', $aWebinar['start_time']);
            $aWebinar['start_hour'] = date('H', $aWebinar['start_time']);
            $aWebinar['start_day'] = date('d', $aWebinar['start_time']);
            $aWebinar['start_month'] = date('m', $aWebinar['start_time']);
            $aWebinar['start_year'] = date('Y', $aWebinar['start_time']);
            $aWebinar['categories'] = Phpfox::getService('webinar.category')->getCategoryIds($aWebinar['webinar_id']);
            $this->_aWebinar[$iId] = $aWebinar;
        }

        return $this->_aWebinar[$iId];
    }

    public function getForProcess($iId){

        if (!isset($this->_aWebinar[$iId])){
            $aWebinar =  $this->database()->select('' . Phpfox::getUserField() . ', w.*')
                ->from($this->_sTable, 'w')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = w.user_id')
                ->where('w.webinar_id = ' . (int) $iId)
                ->execute('getSlaveRow');
            
            if (!isset($aWebinar['webinar_id'])){
                return false;
            }
            $this->_aWebinar[$iId] = $aWebinar;
        }
        return $this->_aWebinar[$iId];
    }

    public function getWebinar($iId)
    {
        if (Phpfox::isModule('like'))
        {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'webinar\' AND lik.item_id = w.webinar_id AND lik.user_id = ' . Phpfox::getUserId());
        }

        $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = w.user_id AND f.friend_user_id = " . Phpfox::getUserId());

        $this->database()->where('w.webinar_id = ' . (int) $iId);

        $aWebinar = $this->database()->select('' . Phpfox::getUserField() . ', w.*')
            ->from($this->_sTable, 'w')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = '.Phpfox::getUserId())
            ->execute('getSlaveRow');

        if (!isset($aWebinar['webinar_id'])){

            return Phpfox_Error::display(Phpfox::getPhrase('webinar.sorry_the_webinar_you_are_looking_for_no_longer_exists', array('link' => Phpfox::getLib('url')->makeUrl('webinar'))));
        }elseif(Phpfox::getService('webinar.process')->isSubscriberBan($aWebinar['webinar_id'], Phpfox::getUserId())){

            return Phpfox_Error::display(Phpfox::getPhrase('webinar.you_were_banned_by_the_moderator', array('link' => Phpfox::getLib('url')->makeUrl('webinar'))));
        }elseif(!empty($aWebinar['is_closed']) && $aWebinar['user_id'] != Phpfox::getUserId() && !$this->isSubscriberJoined($aWebinar['webinar_id'])){

            return Phpfox_Error::display(Phpfox::getPhrase('webinar.this_webinar_is_closed_you_can_not_participate_in_it', array('link' => Phpfox::getLib('url')->makeUrl('webinar'))));
        }elseif(empty($aWebinar['is_closed']) && $aWebinar['user_id'] != Phpfox::getUserId() && !$this->isSubscriberJoined($aWebinar['webinar_id'])){

            $this->addNewSubscriber($iId);
        }
        if (!Phpfox::isModule('like'))
        {
            $aWebinar['is_liked'] = false;
        }
        return $aWebinar;
    }

    private function addNewSubscriber($iId){
        
		if (!$this->isSubscriberJoined($iId)){
		
            return $this->database()->insert(Phpfox::getT('webinar_subscriber'), array('webinar_id'=>$iId, 'user_id'=>Phpfox::getUserId()));
        }
        return false;
    }

    public function getSubscribersCount($iId = null){
        if (is_null($iId) && empty($iId)){
            return $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('webinar_subscriber'), 'ws')
                ->execute('getField');
        }else{
            $this->database()->where('webinar_id = '.(int)$iId);
            return $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('webinar_subscriber'), 'ws')
                ->execute('getField');
        }
    }

    public function getWebinarDetail($iId){


        if ($this->database()->select('COUNT(*)')->from(Phpfox::getT('webinar_category_data'))->where('webinar_id = '.$iId)->execute('getField')){
            $this->database()->clean();
            $this->database()->select('wc.category_id, wc.name AS category_name,wcd.webinar_id,')->leftJoin(Phpfox::getT('webinar_category_data'), 'wcd', 'wcd.webinar_id = w.webinar_id')
                ->leftJoin(Phpfox::getT('webinar_category'), 'wc', 'wc.category_id = wcd.category_id');
        }

        $this->database()->where('w.webinar_id = '.(int)$iId);
        return $this->database()->select('w.webinar_id, w.title, w.image_src, w.time_stamp, w.user_id, u.full_name, w.start_time, u.user_name')
            ->from(Phpfox::getT('webinar'), 'w')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = w.user_id')
            ->execute('getSlaveRow');
    }

    public function isSubscriberJoined($iId, $iUserId=null, $isBanned=false){
        if (empty($iId)){
            return false;
        }
        $this->database()->clean();
        $this->database()->where(sprintf("ws.webinar_id=%s AND ws.user_id=%s AND ws.is_banned ".(empty($isBanned)?"IN(0, 1)":"=0"), $iId, !empty($iUserId)?$iUserId:Phpfox::getUserId()));
        $sSubscriber = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('webinar_subscriber'), 'ws')
            ->execute('getField');

        if (!empty($sSubscriber) && (int)$sSubscriber > 0){

            return true;
        }else{

            return false;
        }
    }

    public function getSectionMenu()
    {
        $aFilterMenu = array();
        if (!defined('PHPFOX_IS_USER_PROFILE'))
        {
            $aFilterMenu = array(
                Phpfox::getPhrase('webinar.all_webinars') => '',
                Phpfox::getPhrase('webinar.my_webinars') => 'mywebinars',
                Phpfox::getPhrase('webinar.me_invited') => 'meinvited',
                Phpfox::getPhrase('webinar.opened_webinars') => 'opened',
            );

            $aFilterMenu[] = true;
        }

        Phpfox_Template::instance()->buildSectionMenu('webinar', $aFilterMenu);
    }


	public function __call($sMethod, $aArguments){
		if ($sPlugin = Phpfox_Plugin::get('webinar.service_webinar__call')){
			eval($sPlugin);
			return;
		}
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}

?>