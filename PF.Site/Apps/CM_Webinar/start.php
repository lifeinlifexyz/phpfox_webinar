<?php
//Instance settings

Phpfox_Setting::instance()->setParam('webinar.via_send', setting('cm_webinar_via_send', 'Message'));
Phpfox_Setting::instance()->setParam('webinar.comment_time_format', setting('cm_webinar_comment_time_format', 'M j, g:i a'));
Phpfox_Setting::instance()->setParam('webinar.comment_ajax_time_refresh', setting('cm_webinar_comment_ajax_time_refresh', '5'));
Phpfox_Setting::instance()->setParam('webinar.comment_wordwrap', setting('cm_webinar_comment_wordwrap', '100'));

event('app_settings', function ($settings){
    if (isset($settings['cm_webinar_enabled'])) {
        \Phpfox::getService('admincp.module.process')->updateActivity('webinar', $settings['cm_webinar_enabled']);
    }
});

group('/webinar', function (){
    route('/admincp', function (){
        auth()->isAdmin(true);
        if(!Phpfox::getService('admincp.product')->isProduct('Webinar')){
            if (file_exists(PHPFOX_DIR_XML.'Webinar.xml')){
                echo('Webinar module not installed, please install the module on the <a href="'.Phpfox::getLib('url')->makeUrl('admincp').'">dashboard</a>');
            } else {
                echo('Webinar module not installed');
            }
            return 'controller';
        }
        if (Phpfox_Module::instance()->isModule('webinar')){
            echo '<script type="text/javascript">$Behavior.admincpEditwebinar = function() { $Core.webinar.url(\'' . Phpfox_Url::instance()->makeUrl('admincp.webinar') . '\'); }</script>';
            Phpfox_Module::instance()->dispatch('webinar.admincp.index');
        } else {
            echo('The module is disabled.');
        }
        return 'controller';
    });
    route('/admincp/add', function (){
        auth()->isAdmin(true);
        if(!Phpfox::getService('admincp.product')->isProduct('Webinar')){
            if (file_exists(PHPFOX_DIR_XML.'Webinar.xml')){
                echo('Webinar module not installed, please install the module on the <a href="'.Phpfox::getLib('url')->makeUrl('admincp').'">dashboard</a>');
            } else {
                echo('Webinar module not installed');
            }
            return 'controller';
        }
        if (Phpfox_Module::instance()->isModule('webinar')){
            Phpfox_Module::instance()->dispatch('webinar.admincp.add');
        } else {
            echo('The module is disabled.');
        }
        return 'controller';
    });
});