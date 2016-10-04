<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Main comment service that we use to retrieve posts.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		CodeMake.Org
 * @package  		Module_Comment
 */
class Webinar_Service_Comment_Comment extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('webinar_comment');
    }

    /**
     * Get the latest comments.
     *
     * @param int $iLimit Define the limit so we don't return all the shoutouts.
     * @return array Array of comments.
     */
    public function getMessages($iWebinarId, $iLimit = 5)
    {
        $this->database()->where('webinar_id = ' . (int) $iWebinarId);
        $aMessages = $this->database()->select('s.comment_id, s.text, s.time_stamp, ' . Phpfox::getUserField())
            ->from($this->_sTable, 's')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = s.user_id')
            ->limit($iLimit)
            ->order('s.time_stamp DESC')
            ->execute('getSlaveRows');

        foreach ($aMessages as $iKey => $aMessage)
        {
            $aMessage['text'] = Phpfox::getLib('parse.output')->replaceHashTags(Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->clean($aMessage['text']), Phpfox::getParam('webinar.comment_wordwrap')));
        }

        return $aMessages;
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
        if ($sPlugin = Phpfox_Plugin::get('webinar.service_comment__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

?>