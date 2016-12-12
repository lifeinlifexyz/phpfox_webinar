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

class Webinar_Component_Block_Categories extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

        if (defined('PHPFOX_IS_USER_PROFILE'))
        {
            return false;
        }

        $sCategory = $this->getParam('sCategory');

        $aCategories = Phpfox::getService('webinar.category')->getForBrowse($sCategory);

        if (!is_array($aCategories))
        {
            return false;
        }

        if (!count($aCategories))
        {
            return false;
        }

        $aCallback = $this->getParam('aCallback', false);
        if ($aCallback !== false)
        {
            $sHomeUrl = '/' . $aCallback['url_home'][0] . '/' . implode('/', $aCallback['url_home'][1]) . '/webinar/';
            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCategories[$iKey]['url'] = preg_replace('/^http:\/\/(.*?)\/webinar\/(.*?)$/i', 'http://\\1' . $sHomeUrl . '\\2', $aCategory['url']);
                if (isset($aCategory['sub']))
                {
                    foreach ($aCategory['sub'] as $iSubKey => $aSubCategory)
                    {
                        $aCategories[$iKey]['sub'][$iSubKey]['url'] = preg_replace('/^http:\/\/(.*?)\/webinar\/(.*?)$/i', 'http://\\1' . $sHomeUrl . '\\2', $aSubCategory['url']);
                    }
                }
            }
        }

        $this->template()->assign(array(
                'sHeader' => ($sCategory === null ? _p('webinar.categories') : _p('webinar.sub_categories')),
                'aCategories' => $aCategories,
                'sCategory' => $sCategory
            )
        );

        return 'block';
    }
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->template()->clean(array(
				'aCategories'
			)
		);
	
		(($sPlugin = Phpfox_Plugin::get('webinar.component_block_categories_clean')) ? eval($sPlugin) : false);
	}	
}

?>