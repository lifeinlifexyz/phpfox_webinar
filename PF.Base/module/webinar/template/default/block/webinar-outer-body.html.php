<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright      [PHPFOX_COPYRIGHT]
 * @author         Kalil uulu Bolot
 * @package        Phpfox_Webinar
 */

defined('PHPFOX') or exit('NO DICE!');
$aWebinar = $this->getVar('aWebinar');

?>
<div class="item_view">
    {if $aWebinar.user_id == Phpfox::getUserId() || Phpfox::isAdmin()}
    <div class="item_info">
        <ul>
            <li>{phrase var='webinar.begin_in'}: <?php echo(Phpfox::getTime('H:i M d, Y', $aWebinar['start_time']));?></li>
        </ul>
    </div>
    <div class="item_bar">
        <div class="item_bar_action_holder">
            <a role="button" data-toggle="dropdown" class="item_bar_action"><span>{phrase var='webinar.actions'}</span></a>
            <ul class="dropdown-menu">
                {template file='webinar.block.menu'}
            </ul>
        </div>
    </div>
    {else}
    <div class="item_info">
        <ul>
            <li>
            <span class="js_webinar_view_like" onclick="$(this).hide(); $('#js_webinar_view_unlike').show();
						$.ajaxCall('like.add', 'type_id=webinar&amp;item_id={$aWebinar.webinar_id}');return false;" id="js_webinar_view_like"{if $aWebinar.is_liked} style="display:none;"{/if}>
            {phrase var='webinar.like'}
            </span>
			<span class="js_webinar_view_unlike" onclick="$(this).hide(); $('#js_webinar_view_like').show();
						$.ajaxCall('like.delete', 'type_id=webinar&amp;item_id={$aWebinar.webinar_id}'); return false;" id="js_webinar_view_unlike" {if !$aWebinar.is_liked} style="display:none;"{/if}>
            {phrase var='webinar.unlike'}
            </span>
            </li>
            <li>{phrase var='webinar.begin_in'}: <?php echo(Phpfox::getTime('H:i M d, Y', $aWebinar['start_time']));?></li>
        </ul>
    </div>
    {/if}
    <div class="item_content item_view_content">
        {if isset($aWebinar.parsed.embed_code)}
            <div class="pf_video_wrapper">
                {$aWebinar.parsed.embed_code}
            </div>
        {/if}
    </div>
</div>