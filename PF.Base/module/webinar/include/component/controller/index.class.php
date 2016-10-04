<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		CodeMake.Org
 * @package 		Module_Webinar
 */

class Webinar_Component_Controller_Index extends Phpfox_Component
{

	public function process(){

        $sView = $this->request()->get('view', false);
        $aWebinarDisplays = array(10, 20, 30);

        $aSort = array(
            'latest' => array('w.time_stamp', Phpfox::getPhrase('webinar.latest')),
            //'most-viewed' => array('w.total_view', Phpfox::getPhrase('webinar.most_viewed')),
            'most-talked' => array('w.total_comment', Phpfox::getPhrase('webinar.most_discussed'))			
        );

        $this->search()->set(array(
                'type' => 'webinar',
                'field' => 'w.webinar_id',
                'search_tool' => array(
                    'table_alias' => 'w',
                    'search' => array(
                        'action' => Phpfox::getLib('url')->makeUrl('webinar.index'),
                        'default_value' => Phpfox::getPhrase('webinar.search_webinars'),
                        'name' => 'search',
                        'field' => 'w.title'
                    ),
                    'sort' => $aSort,
                    'show' => $aWebinarDisplays
                )
            )
        );

        $aBrowseParams = array(
            'module_id' => 'webinar',
            'alias' => 'w',
            'field' => 'webinar_id',
            'table' => Phpfox::getT('webinar'),
            'hide_view' => array('mywebinars', 'meinvited')
        );

        switch ($sView)
        {
            case 'mywebinars':

                Phpfox::isUser(true);
                $this->search()->setCondition('AND w.user_id = ' . Phpfox::getUserId());
                break;
            case 'meinvited':
                Phpfox::isUser(true);
                $this->search()->setCondition('OR ws.user_id = '.Phpfox::getUserId());
                break;
			case 'opened':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND w.is_closed = 0');
                break;
            default:
                $this->search()->setCondition('AND w.is_search = 1');
                break;
        }

        if ($this->request()->get('req2') == 'category')
        {
            $iCategory = $this->request()->getInt('req3');
            $sWhere = 'AND wc.category_id = ' . (int) $iCategory;
            $this->search()->setCondition($sWhere);
        }
        Phpfox::getService('webinar')->getSectionMenu();

        $this->search()->browse()->params($aBrowseParams)->execute();
        $aWebinars = $this->search()->browse()->getRows();
        $aPager = array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount()
        );
        Phpfox::getLib('pager')->set($aPager);
        $this->template()->setTitle(Phpfox::getPhrase('webinar.all_webinars'))
            ->setBreadCrumb(Phpfox::getPhrase('webinar.all_webinars'), Phpfox::getLib('url')->makeUrl('webinar'))
            ->setHeader("cache", array(
                'webinar.css'=>'module_webinar',
                'pager.css' => 'style_css'
            ))->assign(array(
                'aWebinars' => $aWebinars
            )
        );

    }

	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('webinar.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>