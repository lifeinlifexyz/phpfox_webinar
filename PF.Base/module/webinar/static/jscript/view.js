
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
		var aUsers = [], iWebinarId = document.getElementById('webinar_id').value;
		console.log(iWebinarId);
		$.each($('.label_flow input:checked'), function (index, input) {
			if (!in_array(input.value, aSubscribersId)){
				aUsers[input.value] = input.value;
			}
		});
		if (aUsers.length > 0){
			$.ajaxCall('webinar.invite', 'users='+aUsers+'&webinar_id='+iWebinarId);
		}else{
			alert(oTranslations['webinar.please_select_user']);
		}
		
	}
	if (aUsers.length > 0){
		js_box_remove($('.label_flow'));
	}
}
function plugin_cancelSearchFriends() {
	js_box_remove($('.label_flow'));
}
$('#js_block_border_webinar_attendees .content').css({'overflow-y':'scroll', 'height':'210px'});
var widthShoutbox = $('#js_shoutbox_messages').width(); $('#js_shoutbox #js_shoutbox_input').width(widthShoutbox);