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
$aWebinars = $this->getVar('aWebinars');
?>
<?php if (isset($aWebinars) && count($aWebinars)): ?>
    <?php foreach($aWebinars as $aWebinar):?>
        <div id="js_webinar_entry" class="table_row">
            <div class="row_title" style="margin-bottom: 4.3%;">
                <div class="row_title_image" style="cursor: pointer;padding-right:10px;">
                    <?php Phpfox::getService('webinar.template')->image('webinar.image_url', $aWebinar['image_src'], '_75'); ?>
                </div>
                <div class="row_title_info">
                    <div class="webinar_content">
                        <span id="webinar_title">
                            <a href="<?php echo($aWebinar['link']);?>" id="js_webinar_edit_inner_title_<?php echo($aWebinar['webinar_id']);?>" class="link ajax_link"><?php echo(Phpfox::getService('webinar.utils')->text($aWebinar['title'], 50));?></a>
                        </span>
                        <div id="webinar_text">
                            <div class="item_content item_view_content">
                                <?php echo(Phpfox::getService('webinar.utils')->text($aWebinar['description'], 300).'...');?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="webinar_last_post" style="
                <?php
                    if ($aWebinar['start_time']>PHPFOX_TIME):
                        echo('border: 1px #B4E3B9 solid;');
                    else:
                        echo('border: 1px #E3B4B4 solid;');
                    endif;
                ?>">
                <div class="extra_info">
                    <?php echo(_p('webinar.by_full_name_on_time',
                        array(
                            'full_name'=>sprintf('<span class="user_profile_link_span" id="js_user_name_link_%s"><a href="%s">'.Phpfox::getService('webinar.utils')->text($aWebinar['full_name'], 30).'</a></span>', $aWebinar['user_name'], Phpfox::getLib('url')->permalink('webinar', $aWebinar['user_name'])),
                            'time'=>Phpfox::getService('webinar.utils')->convertTime($aWebinar['time_stamp'])
                        )
                    ));
                    ?>
                </div>
                <div class="extra_info">
                    <?php echo(_p('webinar.begin_in_time', array('time'=>Phpfox::getTime('H:i M d, Y', $aWebinar['start_time']))));?>
                </div>
                <div class="extra_info">
                    <?php echo(_p('webinar.webinar_is_closed', array('YesOrNo'=>!empty($aWebinar['is_closed'])?_p('webinar.yes'):_p('webinar.no'))));?>
                </div>
                <div class="extra_info">
                    <?php echo(_p('webinar.total_subscribers_count',
                        array(
                            'count'=>Phpfox::getService('webinar.webinar')->getSubscribersCount($aWebinar['webinar_id'])
                        )
                    ));
                    ?>
                </div>
                <div class="extra_info">
                    <?php echo(_p('webinar.total_comments_count',
                        array(
                            'count'=>$aWebinar['total_comment']
                        )
                    ));
                    ?>
                </div>
                <div class="extra_info">
                    <?php echo(_p('webinar.total_likes_count',
                        array(
                            'count'=>$aWebinar['total_like']
                        )
                    ));
                    ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    {pager}
<?php else:?>
    <div class="error_message" id="js_video_upload_message">
        {phrase var='webinar.no_webinar'}
    </div>
<?php endif;?>