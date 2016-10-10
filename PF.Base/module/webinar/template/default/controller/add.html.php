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

?>
<?php
	$aForms = $this->getVar('aForms');
?>

<form method="post" action="{if !empty($bIsEdit)}{url link='webinar.add.edit' id=$aForms.webinar_id}{else}{url link='webinar.add'}{/if}" enctype="multipart/form-data" name="js_webinar_form">
    {if !empty($bIsEdit)}
        <input id="edit_id" type="hidden" name="edit_id" value="{$aForms.webinar_id}"/>
    {/if}
    {if !empty($bBackAdmincp)}
        <input type="hidden" name="back_admincp" value="{$bBackAdmincp}"/>
    {/if}
    <div class="error_message" style="display: none;" id="js_provide_title"></div>
    <div class="table">
        <div class="table_left">
            <label for="title">{required}{phrase var='webinar.title'}:</label>
        </div>
        <div class="table_right">
            <input type="text" name="val[title]" size="40" value="{if isset($aForms.title)}{$aForms.title}{/if}" id="title"/>
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            <label for="skip_send_invitation">{phrase var='webinar.skip_send_invitation'}:</label>
        </div>
        <div class="table_right">
            <label class="radio-inline"><input type="radio" name="val[skip_send_invitation]" value="1">{phrase var='webinar.yes'}</label>
            <label class="radio-inline"><input type="radio" name="val[skip_send_invitation]" value="0" checked="checked">{phrase var='webinar.no'}</label>
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            <label for="description">{phrase var='webinar.description'}:</label>
        </div>
        <div class="table_right">
            {editor id='description' text=$aForms.description name='val[description]'}
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            <label for="link_to_source">{required}{phrase var='webinar.link_to_source'}:</label>
        </div>
        <div class="table_right">

            <input type="text" name="val[link_to_source]" value="{if isset($aForms.link_to_source)}{$aForms.link_to_source}{/if}" id="link_to_source"/>
            <div class="extra_info">
                {phrase var='webinar.extra_info_source'}
            </div>
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            <label for="start_a_hangout">{phrase var='webinar.create_a_hangout_and_paste_the_link_to_source_field'}:</label>
        </div>
        <div class="table_right">
            <div id="placeholder-div1"></div>
            {literal}
            <script>
                $Behavior.addWebinarReady = function(){
                    gapi.hangout.render('placeholder-div1', {
                        'render': 'createhangout',
                        'hangout_type': 'onair',
                        'initial_apps': [{'app_id': ''}],
                        'widget_size': 175
                    });
                }
            </script>
            {/literal}
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            <label for="start_time">{phrase var='webinar.start_time'}:</label>
        </div>
        <div class="table_right">
            <div style="position: relative;">
                {select_date prefix='start_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+1' time_separator='webinar.time_separator'}
            </div>
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            <label for="category">{phrase var='webinar.category'}:</label>
        </div>
        <div class="table_right">
			{if !empty($sCategories)}
				{$sCategories}
			{/if}
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            {phrase var='webinar.photo'}:
        </div>
        <div class="table_right">			
            {if !empty($bIsEdit) && !empty($aForms.image_src)}
            <div id="js_webinar_current_image">
                <?php Phpfox::getService('webinar.template')->image('webinar.image_url', $aForms['image_src'], '_75'); ?>
                <div class="extra_info">
                    {phrase var='webinar.click_here_to_delete_this_image_and_upload_a_new_one_in_its_place'
                    javascript=$sJavaScriptEditLink}
                </div>
            </div>
            {/if}
            <div id="js_webinar_upload_image" {if !empty($bIsEdit) && !empty($aForms.image_src)} style="display:none;"{/if}>
				<input id="image" type="file" name="image"/>

				<div class="extra_info">
					{phrase var='webinar.you_can_upload_a_jpg_gif_or_png_file' size=$iMaxIconSize}
				</div>
			</div>
		</div>
	</div>	

    <div class="table" id="js_webinar_close">
        <div class="table_left">
            {phrase var='webinar.closed'}:
        </div>
        <div class="table_right">
            <label class="radio-inline"><input type="radio" {if isset($aForms.is_closed) && $aForms.is_closed == 1}checked="checked"{/if} name="val[is_closed]" value="1">{phrase var='webinar.yes'}</label>
            <label class="radio-inline"><input type="radio" {if isset($aForms.is_closed) && $aForms.is_closed == 0}checked="checked"{elseif !isset($aForms.is_closed)}checked="checked"{/if} value="0" name="val[is_closed]">{phrase var='webinar.no'}</label>
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            {phrase var='webinar.can_add_comment'}:
        </div>
        <div class="table_right">
            <div class="table_right">
                <label class="radio-inline"><input type="radio" {if isset($aForms.is_commented) && $aForms.is_commented == 1}checked="checked"{elseif !isset($aForms.is_commented)}checked="checked"{/if} name="val[is_commented]" value="1">{phrase var='webinar.yes'}</label>
                <label class="radio-inline"><input type="radio" {if isset($aForms.is_commented) && $aForms.is_commented == 0}checked="checked"{/if} value="0" name="val[is_commented]">{phrase var='webinar.no'}</label>
            </div>
        </div>
    </div>
    <div class="table">
        <div class="table_right">
            <input type="checkbox" name="val[is_search]" id="is_search" value="1" {if isset($aForms.is_search) && $aForms.is_search == 1} checked="checked" {elseif !isset($aForms.is_search)} checked="checked" {/if}/>
            {phrase var='webinar.show_this_webinar_in_search_results'}
        </div>
    </div>
   
    <div class="table">
        <div class="table_left">
            {phrase var='webinar.invite_subscribers'}:
        </div>
        <div class="table_right" id="chooseSubscribers">
            {if isset($aForms.aSubscribers)}
            {foreach from=$aForms.aSubscribers item=aSubscriber}
                {if !empty($aSubscriber)}
                    <input type="hidden" name="chooseSubscribers[]" value="{$aSubscriber.user_id}" class="v_middle">
                {/if}
            {/foreach}
            {/if}
            <div class="table_right_text">
                (<a href="#"
                    onclick="tb_show(oTranslations['webinar.choose_subscribers'], $.ajaxBox('webinar.blockChooseSubscribers')); return false;">{phrase
                    var='webinar.choose_subscribers'}</a>)
            </div>
            <div class="item_is_active_holder" style="{if !empty($aForms.aSubscribers)}display:block;{else}display:none;{/if}margin-top: 10px;">
                {if isset($aForms.aSubscribers)}
                {foreach from=$aForms.aSubscribers item=aSubscriber}
                    {if !empty($aSubscriber)}
                        <div id="subscriber_name">{$aSubscriber.full_name}<div title="Remove" id="removeMember" onclick="{literal}$(this).parent().remove();$('#chooseSubscribers input[value={/literal}{$aSubscriber.user_id}{literal}]').remove();{literal}">(X)</div></div>
                    {/if}
                {/foreach}
                {/if}
            </div>
        </div>
    </div>
    <div class="table_clear">
        <input id="submitwebinar" type="submit"
               value="{if !empty($bIsEdit)}{phrase var='webinar.update_webinar'}{else}{phrase var='webinar.create_webinar'}{/if}"
               class="button"/>
    </div>
</form>
