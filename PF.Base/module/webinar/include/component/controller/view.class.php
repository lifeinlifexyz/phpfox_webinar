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

class Webinar_Component_Controller_View extends Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);
        $this->template()->setBreadcrumb(Phpfox::getPhrase('webinar.all_webinars'), Phpfox::getLib('url')->makeUrl('webinar'));
        $aWebinar = array();

        if ($iId = $this->request()->get('req3')) {

            if (!empty($iId)) {
                Phpfox::getService('webinar.process')->updateCounterComment($iId);
            }

            $aWebinar = Phpfox::getService('webinar.webinar')->getWebinar($iId);

            $this->template()->setPhrase(array(
                'webinar.invite',
                'webinar.are_you_sure',
                'webinar.webinar_successfully_recorded_on_the_server',
                'webinar.problem_writing_audio_file_to_disk',
                'webinar.record',
				'webinar.please_select_user',
                'webinar.show_broadcast',
                'webinar.reload_page'
            ));
            $this->template()
                ->setTitle($aWebinar['title'])
                ->setBreadcrumb($aWebinar['title'], Phpfox::getLib('url')->permalink('webinar.view', $aWebinar['webinar_id'], $aWebinar['title']));

        }else{
            Phpfox::getLib('url')->send('webinar');
        }

        if ($aWebinar['start_time']>time()){
            $this->template()
                ->setHeader("cache", array(
                    'flipclock.js' => 'module_webinar',
                    'flipclock.css' => 'module_webinar',
                ));
        }

        printf('<script type="text/javascript">$Behavior.onReadyView = function(){window.webinarId = %s; window.tokenName = "%s"; window.token = "%s"; window.webinarTitle = "%s";}</script>',
            $aWebinar['webinar_id'],
            Phpfox::getTokenName(),
            Phpfox::getService('log.session')->getToken(),
            $aWebinar['webinar_id']
        );
        if (filter_var($aWebinar['link_to_source'], FILTER_VALIDATE_URL))
        {
            $aWebinar['parsed'] = Phpfox::getService('link.link')->getLink($aWebinar['link_to_source']);
            $aWebinar['parsed']['embed_code'] = str_replace('http://player.vimeo.com/', 'https://player.vimeo.com/', $aWebinar['parsed']['embed_code']);
        }
        $this->template()
            ->setHeader("cache", array(
                'quick_edit.js' => 'static_script',
                'feed.js' => 'module_feed',
                'view.js' => 'module_webinar',
                'comment.css' => 'style_css',
                'view.css' => 'module_webinar'
            ))->assign(array(
                    'aWebinar' => $aWebinar
                )
            );
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('webinar.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}

?>