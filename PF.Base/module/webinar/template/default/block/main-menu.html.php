<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Kalil uulu Bolot
 * @package          Module_Webinar
 */

?>
<div id="js_is_user_profile">		 			
     <div class="profile_image">
       <div class="profile_image_holder">             		  
	   </div>
     </div>
    <div class="sub_section_menu">
       <ul> 
        {foreach from=$aMainMenu item=aItem}
            <li class="{if isset($aItem.is_selected)} active{/if}">
                <a href="{url link=$aItem.url}" class="ajax_link"{if isset($aItem.icon)} style="background-image:url('{$aItem.icon}');"{/if}>{$aItem.phrase}</a>
            </li>
        {/foreach}
       </ul>
      <div class="clear"></div>    
    </div> 
</div> 	