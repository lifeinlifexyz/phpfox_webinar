function plugin_selectSearchFriends() {
    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
        }
        return false;
    }
    function getMembersId(inputMembers){
        var aMembersId = [];
        $.each(inputMembers, function (index, input){
            aMembersId[index] = input.value;
        });
        return aMembersId;
    }
    function getMembersName(inputMembers){
        var aMembersName = [];
        $.each(inputMembers, function (index, input){
            aMembersName[index] = input.textContent;
        });
        return aMembersName;
    }

    if (sPrivacyInputName == 'subscribers') {

        var aSubscribersId = getMembersId($('#chooseSubscribers input[type=hidden]'));
        $.each($('.label_flow input:checked'), function (index, input) {
            if (!in_array(input.value, aSubscribersId)){
                $('#chooseSubscribers').prepend('<input type="hidden" name="chooseSubscribers[]" value="' + input.value + '" class="v_middle"/>');
                $('#chooseSubscribers .item_is_active_holder').prepend('<div id=subscriber_name>' + $('#js_friend_' + input.value).text() + '<div title=Remove id=removeMember onclick="$(this).parent().remove();$(\'#chooseSubscribers input[value='+input.value+']\').remove();">(X)</div></div>');
                $('#chooseSubscribers .item_is_active_holder').css({'display': 'block'});
            }
        });
    }else if (sPrivacyInputName == 'moderator') {
        if ($('.label_flow input:checked').length != 1){
            alert(oTranslations['webinar.you_can_select_only_one_moderator_who_is_not_subscriber']);
        }
        var aModeratorId = getMembersId($('#chooseModerator input[type=hidden]'));
        $.each($('.label_flow input:checked'), function (index, input) {
            if (!in_array(input.value, aModeratorId) && 1 > aModeratorId.length && $('.label_flow input:checked').length == 1){
                $('#chooseModerator').prepend('<input type="hidden" name="val[moderator_id]" value="' + input.value + '" class="v_middle"/>');
                $('#chooseModerator .item_is_active_holder').prepend('<div id=moderator_name>' + $('#js_friend_' + input.value).text() + '<div title=Remove id=removeMember onclick="$(this).parent().remove();$(\'#chooseModerator input[value='+input.value+']\').remove();">(X)</div></div>');
                $('#chooseModerator .item_is_active_holder').css({'display': 'block'});
            }
        });
        var aSubscribersId = getMembersId($('#chooseSubscribers input[type=hidden]'));
        var aSubscribersName = getMembersName($('#chooseSubscribers #subscriber_name'));
        $.each($('#chooseModerator input[type=hidden]'), function (index, input) {
            if (in_array(input.value, aSubscribersId)){
                input.remove();
            }
        });
        $.each($('#chooseModerator #moderator_name'), function (index, input) {
            if (in_array(input.textContent, aSubscribersName)){
                input.remove();
            }
        });
        if ($('.label_flow input:checked').length == 1){
            if (in_array($('.label_flow input:checked')[0].value, aSubscribersId)){
                alert(oTranslations['webinar.this_user_is_subscriber_please_choose_different_member']);
            }
        }
    }
    js_box_remove($('.label_flow'));
}
function plugin_cancelSearchFriends() {
    js_box_remove($('.label_flow'));
}

$Behavior.imageCategoryListing = function()
{
    $('.js_mp_category_list').change(function()
    {
        var iParentId = parseInt(this.id.replace('js_mp_id_', ''));

        $('.js_mp_category_list').each(function()
        {
            if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
            {
                $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

                this.value = '';
            }
        });

        $('#js_mp_holder_' + $(this).val()).show();
    });

}
