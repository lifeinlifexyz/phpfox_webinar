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
{if $bIsDisabled == true}
{phrase var='webinar.comments_turned_off_by_webinar_owner'}
{else}
<script type="text/javascript">
{literal}
	function addComment(oObj)
	{
		if ($('#js_comment_input').val() == '')
		{
			return false;
		}
		
		$('#js_comment_form').hide();
		{/literal}
		$('#js_comment_message').html($.ajaxProcess('{phrase var='webinar.adding_your_comment'}'));
		{literal}
		
		$(oObj).ajaxCall('webinar.addComment');
		
		return false;
	}
{/literal}
</script>
<div id="js_comment_form" style="margin-bottom: 20px;">
	<form method="post" action="{url link='current'}" onsubmit="return addComment(this);">
		<div><input type="hidden" value="{$iWebinarId}" name="webinar_id" /></div>
		<div><input id="js_comment_input" type="text" name="comment" size="20" maxlength="255" value="" class="comment_input" /></div>
	</form>
</div>
<div id="js_comment_message"></div>

<div id="js_comment_messages" class="label_flow comment_holder">

	<script type="text/javascript">
		var bLoadCommentOnce = false;
		$Behavior.loadShouts = function()
		{l}
			if (bLoadCommentOnce){l}
				return;
			{r}
			bLoadCommentOnce = true;
			setTimeout("$.ajaxCall('webinar.getMessages', 'webinar_id={$iWebinarId}')", 5000);
		{r};
	</script>
	{foreach from=$aComments item=aComment key=iCommentCount name=comment}
	{template file='webinar.block.comment.entry'}
	{/foreach}
</div>


<script type="text/javascript">
	$Behavior.refreshShouts = function()
	{l}
		if (typeof $Core.Comment == 'undefined')
		{literal}
		{
			$Core.Comment = {};
			$Core.Comment.sParams = '';
		}
		{/literal}
		$Core.Comment.sParams = {if !empty($iWebinarId)}'webinar_id={$iWebinarId}'{else}''{/if};

		setTimeout("$.ajaxCall('webinar.getMessages', $Core.Comment.sParams, 'GET');", {$iCommentRefresh});
	{r};
</script>
{/if}