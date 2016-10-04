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

?>
{literal}
    <script type="text/javascript">
        window.onload = function()
        {
            setTimeout("$.ajaxCall('webinar.getAttendees', 'webinar_id={/literal}{$aWebinar.webinar_id}{literal}', 'GET')", 5000);
        };
    </script>
{/literal}
<ul class="list-group">

{if !empty($aMembers)}
    {if !empty($aMembers.publisher.user_id)}
        <span class="label label-success">{phrase var='webinar.publisher'}</span>
        <a href="{$aMembers.publisher.profile_url}" class="list-group-item clearfix">{$aMembers.publisher.full_name}</a>
    {/if}
    {if !empty($aMembers.subscribers)}
    <span class="label label-primary">{phrase var='webinar.subscribers'}</span>
        {foreach from=$aMembers.subscribers item=aSubscriber}
            <a href="#" class="list-group-item clearfix" {if $aSubscriber.user_id == Phpfox::getUserId()}style="color:green;"{/if}>
              <a target="_blank" href="{$aSubscriber.profile_url}">{$aSubscriber.full_name}</a>
              <span class="pull-right">
                {if !empty($aMembers.publisher) && $aMembers.publisher.user_id == Phpfox::getUserid()}
                  <button style="margin-bottom: 2px;" onclick="if (confirm(oTranslations['webinar.are_you_sure'])){l}$.ajaxCall('webinar.subscriberBan', 'webinar_id={$aSubscriber.webinar_id}&user_id={$aSubscriber.user_id}'){r}else{l}return false;{r}" class="btn btn-xs btn-warning">X</button>
                {/if}
              </span>
            </a>
        {/foreach}
    {/if}
{/if}
</ul>