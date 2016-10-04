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

$aWebinarDetail = $this->getVar('aWebinarDetail');
?>
<div class="sub_section_menu">
    <div class="t_center">
        <?php Phpfox::getService('webinar.template')->image('webinar.image_url', $aWebinarDetail['image_src'], '_120'); ?>
    </div>
    <div class="info">
        <div class="info_left">{phrase var='webinar.created_by'}:</div>
        <div class="info_right">{$aWebinarDetail|user:'':'':50:'':'author'}</div>
    </div>
    <div class="info">
        <div class="info_left">{phrase var='webinar.created_at'}:</div>
        <div class="info_right">{$aWebinarDetail.time_stamp|convert_time}</div>
    </div>
    {if !empty($aWebinarDetail.category_name)}
    <div class="info">
        <div class="info_left">{phrase var='webinar.category'}:</div>
        <div class="info_right">
            <a href="{$aWebinarDetail.category_url}">{$aWebinarDetail.category_name|convert|clean}</a>
        </div>
    </div>
    {/if}
    <div class="info">
        <div class="info_left">{phrase var='webinar.begin_in'}:</div>
        <div class="info_right">
            <?php echo(Phpfox::getTime('H:i M d, Y', $aWebinarDetail['start_time']));?>
        </div>
    </div>
</div>