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

class Webinar_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function blockChooseSubscribers()
    {
        return Phpfox::getBlock('friend.search', array('input'=>'subscribers'));
    }

    public function blockInviteSubscribers(){
        return Phpfox::getBlock('friend.search', array('input'=>'subscribers'));
    }
    
    public function invite(){
        $aUsers = $this->get('users');
        $iWebinarId = $this->get('webinar_id');
        if (is_string($aUsers) && !empty($iWebinarId)){
            $aUsers = explode(',', $this->get('users'));
            $aUsers = array_diff($aUsers, array(""));
        }

        if(!Phpfox::getService('webinar.process')->inviteMembersVia((int)$iWebinarId, $aUsers, true)){
            Phpfox::getLib('ajax')->alert(_p('webinar.selected_users_have_been_invited'));
        }
    }
    public function removeLogoWebinar()
    {
		$iId = $this->get('id');
		if (empty($iId)){
			return false;
		}
		if (!Phpfox::getService('webinar.process')->removeWebinarLogo($iId)){
			$this->alert(_p('webinar.failed_to_delete_the_image'));
		}
    }

    public function getAttendees(){
        if (!Phpfox::getService('webinar.process')->isPublisher($this->get('webinar_id'))){
            if (!Phpfox::getService('webinar.webinar')->isSubscriberJoined($this->get('webinar_id'), null, true)) {
                printf("window.location.href='%s';", Phpfox::getLib('url')->permalink('webinar.view', $this->get('webinar_id')));
                return false;
            }
        }
        list($aWebinar, $aMembers) = Phpfox::getService('webinar.process')->getAllActiveMembersOnWebinar($this->get('webinar_id'));

        $this->template()->assign(array(
                'aMembers' => $aMembers,
                'aWebinar' => $aWebinar
            )
        )->getTemplate('webinar.block.attendees');
        $this->html('#js_block_border_webinar_attendees .content', $this->getContent(false));
        echo('setTimeout("$.ajaxCall(\'webinar.getAttendees\', \'webinar_id='.$this->get('webinar_id').'\',\'GET\');", 5000);');
    }
    public function subscriberBan(){
        $aUser = Phpfox::getService('user')->get($this->get('user_id'));
        if (Phpfox::getService('webinar.process')->subscriberBan($this->get('webinar_id'), $this->get('user_id'))){
            Phpfox::getLib('ajax')->alert(_p('webinar.subscriber_full_name_banned', array('full_name'=>$aUser['full_name'])));
			//$this->call("$('#alert_message #public_message').text('" . _p('webinar.subscriber_full_name_banned', array('full_name'=>$aUser['full_name'])) . "').fadeIn(1000).fadeOut(6000);");
        }else{
            Phpfox::getLib('ajax')->alert(_p('webinar.ban_subscriber_full_name_failed', array('full_name'=>$aUser['full_name'])));
			//$this->call("$('#alert_message #public_message').text('" . _p('webinar.ban_subscriber_full_name_failed', array('full_name'=>$aUser['full_name'])) . "').fadeIn(1000).fadeOut(6000);");
        }
    }

    /**
     * Add a new comment
     *
     * @return boolean Return false if we ran into an error.
     */
    public function addComment()
    {
        // Only members allowed to add a comment
        Phpfox::isUser(true);

        // Run last_post SPAM check
        if (Phpfox::getLib('spam')->check(array(
                'action' => 'last_post',
                'params' => array(
                    'field' => 'time_stamp',
                    'table' => Phpfox::getT('webinar_comment'),
                    'condition' => 'user_id = ' . Phpfox::getUserId(),
                    'time_stamp' => Phpfox::getUserParam('webinar.flood_control_webinar_comments')
                )
            )
        )
        )
        {
            // Reset the shoutbox form
            $this->show('#js_comment_form')
                ->hide('#js_comment_message')
                ->focus('#js_comment_input');

            // Send them a message that they failed the flood control
            $this->alert(_p('webinar.please_wait_limit_seconds_before_adding_a_new_comment', array('limit' => Phpfox::getUserParam('webinar.flood_control_webinar_comments'))));

            return false;
        }

        if (Phpfox::getLib('parse.format')->isEmpty($this->get('comment')))
        {
            $this->show('#js_comment_form')
                ->hide('#js_comment_message')
                ->val('#js_comment_input', '')
                ->focus('#js_comment_input')
                ->alert(_p('webinar.enter_a_comment'));

            return false;
        }

        // Add the comment
        if ($iId = Phpfox::getService('webinar.comment.process')->add(Phpfox::getUserId(), $this->get('comment'), $this->get('webinar_id', null)))
        {
            // Get all the default user fields we use
            $sFields = Phpfox::getUserField();
            // Create an array of the string fields
            $aFields = explode(',', $sFields);

            $aParams = array();
            foreach ($aFields as $sField)
            {
                // Replace database alias
                $sField = trim(str_replace('u.', '', $sField));
                // Cache the fields and get the current users actual value
                $aParams[$sField] = Phpfox::getUserBy($sField);
            }

            // Shorten the text
            // Clean the text, we don't allow HTML
            $sText = Phpfox::getLib('parse.output')->replaceHashTags(Phpfox::getLib('parse.output')->split(Phpfox::getLib('parse.output')->clean(Phpfox::getLib('parse.input')->clean($this->get('comment'), 255)), Phpfox::getParam('webinar.comment_wordwrap')));

            // Create the needed template variables not defined with $aParams
            $aMessage = array(
                'comment_id' => $iId,
                'time_stamp' => PHPFOX_TIME,
                'text' => $sText
            );
            Phpfox::getService('webinar.process')->updateCounterComment($this->get('webinar_id'));
            // Assign the variables for the template and get the template
            $this->template()->assign(array(
                    'bCommentAjax' => true,
                    'aComment' => array_merge($aMessage, $aParams), // Merge the arrays to create on variable
                    'iCommentWordWrap' => 100
                )
            )->getTemplate('webinar.block.comment.entry');

            // Add the message to the shoutbox and reset the shoutbox form
            $this->call('$(\'.js_comment_messages\').removeClass(\'row_first\');')
                ->prepend('#js_comment_messages', $this->getContent(false))
                ->show('#js_comment_form')
                ->hide('#js_comment_message')
                ->val('#js_comment_input', '')
                ->focus('#js_comment_input');
        }
    }

    public function delete()
    {
        if (Phpfox::getService('webinar.comment.process')->delete($this->get('id')))
        {
            $this->remove('#js_comment_'.$this->get('id'));
        }
    }

    /**
     * Get the latest comment messages.
     *
     */
    public function getMessages()
    {
        $iWebinarId = $this->get('webinar_id', null);
        $aComments = Phpfox::getService('webinar.comment')->getMessages($iWebinarId, 5);
        foreach ($aComments as $iKey => $aComment)
        {
            // Assign the needed variables for each shoutout
            $this->template()->assign(array(
                    'iCommentCount' => $iKey,
                    'aComment' => $aComment,
                    'iCommentWordWrap' => 100
                )
            )->getTemplate('webinar.block.comment.entry');
        }
        $this->html('#js_comment_messages', $this->getContent(false));
        $this->call('setTimeout("$.ajaxCall(\'webinar.getMessages\', (typeof $Core.Comment != \'undefined\' && typeof $Core.Comment.sParams != \'undefined\') ? $Core.Comment.sParams : ' . (!empty($iWebinarId) ?  '\'webinar_id=' . $iWebinarId . '\'' : '\'\'') . ', \'GET\');", ' . (Phpfox::getParam('webinar.comment_ajax_time_refresh')*1000) . ');');
    }
}

?>