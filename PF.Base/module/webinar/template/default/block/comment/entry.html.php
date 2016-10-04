<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		CodeMake.Org
 * @package  		Module_Webinar
 */
defined('PHPFOX') or exit('NO DICE!'); 

?>
	<div id="js_comment_{$aComment.comment_id}" class="js_webinar_messages {if isset($bCommentAjax)}row2 row_first{else}{if is_int($iCommentCount/2)}row1{else}row2{/if}{if ($iCommentCount + 1) == 1} row_first{/if}{/if}" style="position:relative;">
	{if Phpfox::getUserParam('webinar.can_delete_all_comments') || $aComment.user_id == Phpfox::getUserId()}
	<div style="position:absolute; right:1px">
		<a href="#" onclick="if (confirm('{phrase var='core.are_you_sure' phpfox_squote=true}')) {left_curly} $(this).parents('.js_comment_message:first').remove(); $.ajaxCall('webinar.delete', 'id={$aComment.comment_id}'); {right_curly} return false;" title="{phrase var='webinar.delete_this_comment'}">{img theme='misc/delete.gif'}</a>
	</div>
	{/if}
		{* img user=$aComment suffix='_50_square' max_width=20 max_height=20 style='vertical-align:middle;' *} {$aComment|user:'':'':30}
		<div class="extra_info">
			{$aComment.time_stamp|date:'webinar.comment_time_format'}
		</div>
		<div class="p_4">
			{$aComment.text}
		</div>
	</div>