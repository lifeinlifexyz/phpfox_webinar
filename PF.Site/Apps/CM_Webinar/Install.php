<?php
namespace Apps\CM_Webinar;

use Core\App;

/**
* Class Install
* @author  CodeMake.Org
* @version 4.5.0
* @package Apps\CM_Webinar
*/
class Install extends App\App
{
    private $_app_phrases = [

    ];
    protected function setId()
    {
        $this->id = 'CM_Webinar';
    }
    protected function setAlias()
    {
        $this->alias = 'cmwebinar';
    }
    protected function setName()
    {
        $this->name = 'CM Webinar App';
    }
    protected function setVersion() {
        $this->version = '1.0.1';
    }
    protected function setSupportVersion() {
        $this->start_support_version = '4.2.0';
        $this->end_support_version = '4.5.0';
    }
    protected function setSettings() {
        $this->settings = ['cm_webinar_enabled' => ['info' => 'Webinar App Enabled','type' => 'input:radio','value' => '1','js_variable' => '1',],'cm_webinar_via_send' => ['info' => 'Invite subscribers via','type' => 'select','options' => ['Notification' => 'Notification','Message' => 'Message',],'js_variable' => '1',],'cm_webinar_comment_time_format' => ['info' => 'Format display time of comments','value' => 'M j, g:i a','js_variable' => '1',],'cm_webinar_comment_ajax_time_refresh' => ['info' => 'Refresh time of comments via ajax in seconds','value' => '5','js_variable' => '1',],'cm_webinar_comment_wordwrap' => ['info' => 'Comment word wrap','value' => '100','js_variable' => '1',],];
    }
    protected function setUserGroupSettings() {}
    protected function setComponent() {}
    protected function setComponentBlock() {}
    protected function setPhrase() {
        $this->phrase = $this->_app_phrases;
    }
    protected function setOthers() {
        $this->admincp_route = '/webinar/admincp';
        $this->admincp_menu = ['Manage Categories' => '/webinar',];
        $this->admincp_action_menu = ['/webinar/admincp/add' => 'New Category',];
//        $this->icon = '//cdn.codemake.org/phpfox/webinar/webinar_logo.png';
        $this->_publisher = 'CodeMake.Org';
        $this->_publisher_url = 'http://codemake.org/';
    }
    public $store_id = '1597';
    public $vendor = 'CodeMake.Org - See all our products <a href="//store.phpfox.com/techie/u/ecodemaster" target=_new>HERE</a> - contact us at: support@codemake.org';
}