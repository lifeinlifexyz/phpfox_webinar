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
<?php if(!Phpfox::getService('admincp.product')->isProduct('Webinar')): ?>
		Webinar module not installed, please install the module on the <a href="<?php echo Phpfox::getLib('url')->makeUrl('admincp');?>">dashboard</a>
<?php else:?>
	{if Phpfox::isModule('webinar')}
	<div id="js_menu_drop_down" style="display:none;">
		<div class="link_menu dropContent" style="display:block;">
			<ul>
				<li><a class="popup" href="#" onclick="return $Core.webinar.action(this, 'edit');">{phrase var='webinar.edit'}</a></li>
				<li><a class="popup" href="#" onclick="return $Core.webinar.action(this, 'delete');">{phrase var='webinar.delete'}</a></li>
			</ul>
		</div>
	</div>
	<div class="table_header">
		{phrase var='webinar.categories'}
	</div>
	<form method="post" action="{url link='admincp.webinar'}">
		<div class="table">
			<div class="sortable">
				{$sCategories}
			</div>
		</div>
		<div class="table_clear">
			<input type="submit" value="{phrase var='webinar.update_order'}" class="button" />
		</div>
	</form>
	{else}
	The module is disabled.
	{/if}
<?php endif;?>