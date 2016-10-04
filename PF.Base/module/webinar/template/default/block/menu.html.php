<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!');
?>
<li><a href="#" onclick="tb_show(oTranslations['webinar.invite'], $.ajaxBox('webinar.blockInviteSubscribers')); return false;">{phrase var='webinar.invite_members'}</a></li>
<li><a href="{url link='webinar.add.edit' id=$aWebinar.webinar_id}">{phrase var='webinar.edit'}</a></li>
<li class="item_delete"><a href="{url link='webinar.add.delete' id=$aWebinar.webinar_id}" class="sJsConfirm">{phrase var='webinar.delete'}</a></li>