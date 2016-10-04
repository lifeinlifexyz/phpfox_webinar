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

class Webinar_Service_Comment_Process extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('webinar_comment');
    }

    /**
     * Add a new comment.
     *
     * @param int $iUserId User ID of the user.
     * @param string $sText Shoutout text.
     *
     * @return boolean Return TRUE by default
     */
    public function add($iUserId, $sText, $iWebinarId = null)
    {
        Phpfox::getService('ban')->checkAutomaticBan($sText);
        // Clean the text, we don't allow HTML here.
        $sText = Phpfox::getLib('parse.input')->clean($sText, 255);

        $aSql = array(
            'user_id' => (int) $iUserId,
            'text' => $sText,
            'time_stamp' => PHPFOX_TIME,
            'webinar_id' => $iWebinarId
        );

        $iInsert = $this->database()->insert($this->_sTable, $aSql);
        if (defined('PHPFOX_USER_IS_BANNED') && PHPFOX_USER_IS_BANNED)
        {
            return false;
        }
        return $iInsert;
    }

    /**
     * Used via a cron job and clears a certain amount of shoutouts
     * from the shoutbox table so we keep this table slim.
     *
     * Notice: We use PHP to contorl how many shoutouts we DELETE as more tests
     * will be needed with our database drivers on the proper support for LIMITING a DELETE
     * query that SQL standards support. (EG. DELETE FROM table WHERE id = 1 LIMIT 1
     *
     * @param int $iLimit Define how many shoutouts we should keep.
     */
    public function clear($iLimit = 100)
    {
        $aComments = $this->database()->select('comment_id')
            ->from($this->_sTable)
            ->order('time_stamp DESC')
            ->execute('getRows');

        foreach ($aComments as $iKey => $aComment)
        {
            if ($iKey < $iLimit)
            {
                continue;
            }

            $this->database()->delete(Phpfox::getT('webinar_comment'), 'comment_id =' . $aComment['comment_id']);
        }
    }

    public function delete($iId)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('webinar.can_delete_all_comments', true);
        $this->database()->delete($this->_sTable, 'comment_id = ' . (int) $iId);
        return true;
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