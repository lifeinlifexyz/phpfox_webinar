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
$aWebinar = $this->getVar('aWebinar');

?>
<?php if ($aWebinar['user_id'] == Phpfox::getUserId() || $aWebinar['start_time'] < time()):?>
<input type="hidden" id="webinar_id" name="webinar_id" value="{$aWebinar.webinar_id}"/>
<div class="item_view">
    <div id="load" style="display: none;"><?php Phpfox::getService('webinar.template')->image('webinar.module_image_url', 'add.gif'); ?></div>
    <div id="alert_message">
        <div class="public_message" id="public_message" style="display: none;"></div>
    </div>

    <div id="js_webinar_outer_body">
        {template file='webinar.block.webinar-outer-body'}
    </div>
</div>
<?php elseif($aWebinar['start_time'] > time()): ?>
    {template file='webinar.block.flipclock'}
<?php endif; ?>