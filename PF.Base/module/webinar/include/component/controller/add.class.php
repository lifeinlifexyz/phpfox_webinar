<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright      [PHPFOX_COPYRIGHT]
 * @author         CodeMake.Org
 * @package        Module_Webinar
 */

class Webinar_Component_Controller_Add extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $this->template()
             ->setBreadcrumb(Phpfox::getPhrase('webinar.module_webinar'), $this->url()->makeUrl('webinar'))
             ->setHeader("cache", array(
                'add.js' =>'module_webinar',
                'add.css'=>'module_webinar'
             ));
        $this->template()->setPhrase(array(
            'webinar.choose_subscribers',
            'webinar.you_can_select_only_one_moderator_who_is_not_subscriber',
            'webinar.this_user_is_subscriber_please_choose_different_member'
        ));

        $bIsEdit = false;
        $sAction = $this->request()->get('req3');
        $aVals = $this->request()->getArray('val');
        if (!empty($aVals)){

            if (isset($aVals['link_to_source']) && filter_var($aVals['link_to_source'], FILTER_VALIDATE_URL)){
                if (preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.?be\/?(.+)?)\/?(watch)?\??(v=(.+))?.+$/', $aVals['link_to_source'])) {

                } elseif (preg_match("/https?:\/\/(?:www\.)?vimeo\.com\/(\d{8})/", $aVals['link_to_source'])) {

                } else {
                    unset($aVals['link_to_source']);
                }
            }else{
                unset($aVals['link_to_source']);
            }
        }

        if ($this->request()->getArray('chooseSubscribers')){
            $aVals['chooseSubscribers'] = $this->request()->getArray('chooseSubscribers');
        }
        $sBackAdmincp = $this->request()->get('back_admincp');
        if (isset($sBackAdmincp) && $sBackAdmincp == 'admincp') {
            $bBackAdmincp = true;
        } else {
            $bBackAdmincp = false;
        }

        if ($sAction === "") {
            $this->template()->setBreadcrumb(Phpfox::getPhrase('webinar.menu_webinar_create_a_webinar_2ab2693ac3f523b9a3033ea8412a1882'), $this->url()->makeUrl('webinar.add'));
        }

        if ($sAction === "edit") {
            if (($iEditId = $this->request()->getInt('id')) || ($iEditId = $this->request()->getInt('edit_id'))) {

                if ($aWebinar = Phpfox::getService('webinar.webinar')->getForEdit($iEditId)) {

                    $this->template()->setBreadcrumb(Phpfox::getPhrase('webinar.edit_webinar'), $this->url()->makeUrl('webinar.add.edit', array('id' => $aWebinar['webinar_id'])));
                    $this->template()->setBreadcrumb(Phpfox::getService('webinar.utils')->text($aWebinar['title'], 25, '...'), $this->url()->makeUrl('webinar.view', array($aWebinar['webinar_id'])));
                    $this->template()
                        ->setHeader("cache", array(
                            '<script type="text/javascript">$Behavior.webinarEditCategory = function(){var aCategories = explode(\',\', \'' . $aWebinar['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); }}</script>',
                        ));
                    $bIsEdit = true;

                    $this->template()->assign(array(
                        'aForms' => $aWebinar,
                        'bIsEdit' => $bIsEdit,
                        'sJavaScriptEditLink' => ($bIsEdit ? "if (confirm('" . Phpfox::getPhrase('webinar.are_you_sure', array('phpfox_squote' => true)) . "')) { $('#js_webinar_upload_image').show(); $('#js_webinar_current_image').remove(); $.ajaxCall('webinar.removeLogoWebinar', 'id={$aWebinar['webinar_id']}'); } return false;" : '')
                    ));
                }
            }
        }

        if ($sAction === "delete") {

            if (Phpfox::getService('webinar.process')->delete($this->request()->getInt('id'))) {
                $this->url()->send('webinar', null, Phpfox::getPhrase('webinar.webinar_successfully_deleted'));
            }

        }

        if (empty($bIsEdit) && !Phpfox::getUserParam('webinar.enable_creating_of_webinars')) {
            $this->url()->send('webinar', null, Phpfox::getPhrase('webinar.current_time_cannot_be_created_new_webinar'));
        }


        $aValidation = array(
            'title' => Phpfox::getPhrase('webinar.provide_a_name_for_this_webinar'),
            'link_to_source' => Phpfox::getPhrase('webinar.please_provide_correct_url')
        );

        $oValidator = Phpfox::getLib('validator')->set(array(
            'sFormName' => 'js_webinar_form',
            'aParams' => $aValidation
        ));

        if ($aVals) {
            if ($oValidator->isValid($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('webinar.process')->update($aWebinar['webinar_id'], $aVals)) {
                        if($sAction == 'edit'){
                            if ($this->request()->get('back_admincp')) {
                                $this->url()->send('admincp.webinar.manage', null, Phpfox::getPhrase('webinar.webinar_successfully_updated'));
                            }
                            $this->url()->send('webinar.view', array($aWebinar['webinar_id']), Phpfox::getPhrase('webinar.webinar_successfully_updated'));
                        }
                    }
                } else {
                    if ($iId = Phpfox::getService('webinar.process')->add($aVals)) {
                        $this->url()->send('webinar.view', array($iId), Phpfox::getPhrase('webinar.webinar_successfully_added'));
                    }
                }
            }else{
                if (empty($bIsEdit)){

                    $this->template()->assign('aForms', $aVals);
                }
            }
        }

        $sCategories = Phpfox::getService('webinar.category')->get();
        $this->template()
            ->setEditor()
            ->setHeader(array(
                'platform.js' => 'module_webinar',
            ))
            ->assign(
            array("sCategories" => $sCategories,
                  "bBackAdmincp" => $bBackAdmincp,
		  "iMaxIconSize" => Phpfox::getUserParam('webinar.webinar_max_upload_icon_size')
            ));

    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('webinar.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}

?>
