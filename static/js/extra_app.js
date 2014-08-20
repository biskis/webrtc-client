var socket = io.connect(socketio_url);

//LEFT SIDE
$(function(){
	socket.emit('join', my_username, usernames_from_users(friends));
	
	$('.search_text').keyup(function(){
		users_search();
	});
	//handlebar
	var handlebar_source_users_list_one   = $("#users-list-template").html();
	handlebar_template_users_list_one = Handlebars.compile(handlebar_source_users_list_one);	
	
	//for modal
	var handlebar_source_users_list_one_modal   = $("#users-list-template-modal").html();
	handlebar_template_users_list_one_modal = Handlebars.compile(handlebar_source_users_list_one_modal);	
	
	var handlebar_source_user_profile_one   = $("#user-profile-template").html();
	handlebar_template_user_profile_one = Handlebars.compile(handlebar_source_user_profile_one);	
	
	var handlebar_source_chat_message_other   = $("#chat-message-other-template").html();
	handlebar_template_chat_message_other = Handlebars.compile(handlebar_source_chat_message_other);	
	var handlebar_source_chat_message_me   = $("#chat-message-me-template").html();
	handlebar_template_chat_message_me = Handlebars.compile(handlebar_source_chat_message_me);	
	
	//add users to left first.
	users_add_to_left(friends, 'Friends');
})
users_search = function(){
	if($("#modalAddUsers").is(":visible")){
		$('.search_text').val($('#modalAddUsers .search_text').val())
	}
	if($('.search_text').val().length == 0) {
		users_add_to_left(friends, 'Friends');
	} else {
		$('.users-list-content').html('<li>Searching</li>');
		$.post(
			api_url + 'user/search',
			{
				'apikey': 'awerty',
				'text'	: $('.search_text').val()
			}, function(o){
				$('.users-list-content').removeClass('searching');
				users_add_to_left(o.result, 'Search');
			}, 'json'
		
		)
	}
}

users_add_to_left = function(users, type){
	$(".users-list").removeClass('friends').removeClass('search').addClass(type.toLowerCase());
	$(".users-list-content").html('');
	$(".users-list-header").html(type);
	if(users.length == 0){
		$(".users-list-content").html('No users');
	} else {
		for(i in users) {
			//add users to left with pending status.
			var context = { user: users[i] };
			var html    = handlebar_template_users_list_one(context);
			$(".left-side .users-list-content").append(html);
			var html_modal    = handlebar_template_users_list_one_modal(context);
			$("#modalAddUsers .users-list-content").append(html_modal);		
			//send to nodejs.
		}
		only_usernames = usernames_from_users(users);
		socket.emit('get_users_online', only_usernames);
	}
}
usernames_from_users = function(users){
	var only_usernames = [];
	for(i in users){
		only_usernames.push(users[i]._id);
	}
	return only_usernames;
}
socket.on('users_online', function (usernames_online){
	$('.users-list-content li[data-user-id]').each(function(){
		$(this).removeClass('online').removeClass('offline');
		var id = $(this).attr('data-user-id');
		if(usernames_online[id] == 'online'){
			$(this).addClass('online');
		} else {
			$(this).addClass('offline');
		}
	});
});
socket.on('user_disconnected', function(username){
	$('[data-user-id=' + username + ']').removeClass('online').addClass('offline');
});
socket.on('user_online', function(username){
	$('[data-user-id=' + username + ']').removeClass('offline').addClass('online');
})









//RIGHT SIDE
user_change_save = function(){
	$.post(
		api_url + 'user/save',
		{
			'apikey'			: 'awerty',
			'name'				: $('#user_change_name').val(),
			'password'			: $('#user_change_password').val(),
			'custom_username' 	: $('#user_change_username').val(),
			'avatar_url'		: $('#user_change_avatar_url').val(),
			'auto_answer'		: $('#user_change_s_auto_answer:checked').length,
			'auto_save'			: $('#user_change_s_auto_save:checked').length
		}, function(o){
			$('.user_change_data').hide();
			noty({
				type:"success", 
				text : "All changes are saved",
				timeout: 5000,
				killer: true
			});
			console.log(o);
		}, 'json'
	)
}
user_show_profile = function(user){
	var context = { user: user };
	var html    = handlebar_template_user_profile_one(context);
	$('.right-side .panels-here .panel').hide();
	$('.right-side .panels-here .user_change_data').hide();
	$('.right-side .panels-here .user_profile').remove();
	$('.right-side .panels-here').append(html);
}
user_show_profile_by_id  = function(user_id){
	var user_html = $('.users-list-content li[data-user-id=' + user_id + ']');
	if(user_html){
		var user = {
			_id : user_html.attr('data-user-id'),
			name: user_html.attr('data-user-name'),
			avatar: user_html.attr('data-user-avatar'),
			custom_username: user_html.attr('data-user-custom_username'),
			is_friend : user_html.attr('data-user-is_friend'),
			is_online : user_html.hasClass('online')
		}
		user_show_profile(user);
	}
}
user_friend_action = function(friend_id, action){
	$.post(
		api_url + 'user/friend',
		{
			'apikey'			: 'awerty',
			'friend_id'			: friend_id,
			'action'			: action
		}, function(o){
			if(o.result && o.result._id){
				if(o.result.is_friend) {
					friends[o.result._id] = o.result;
				} else {
					delete friends[o.result._id];
				}
				users_add_to_left(friends, 'Friends');
				user_show_profile(o.result)
			}
		}, 'json'
	)
}
logout = function(){
	eraseCookie('lses');
	document.location = document.location;
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}