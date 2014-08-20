<!-- modal show call -->
<div class="modal fade" id="modalCall" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Call from <span class="call_from_user-name"></span></h4>
			</div>
			<div class="modal-body">
				<img class="call_from_user-avatar" src="" alt="" height="30"> <span class="call_from_user-name"></span> is calling you...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-error" data-dismiss="modal" onclick="hangup();">Reject</button>
				<button type="button" class="btn btn-success" data-dismiss="modal" onclick="webrtc_start(false);">Answer</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalNotOnline" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Not online</h4>
			</div>
			<div class="modal-body">
				The person you call must be online.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalFileUpload" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">File upload</h4>
				Progress: <span class="current_progress">0</span>/<span class="total_progress">0</span>
			</div>
			<div class="modal-body">
				<input type="file" name="send_file" id="send_file" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalAddUsers" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Add users</h4>
			</div>
			<div class="modal-body">
				<div class="search"><input type="text" placeholder="Search for users" class="form-control search_text" /></div>
				<div class="users-list">
					<div class="users-list-header">Friends</div>
					<ul class="users-list-content list-unstyled">
						<li>Loading...</li>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- end modal -->
<!-- handlebars templates -->
<script id="users-list-template" type="text/x-handlebars-template">
	<li data-user-id="{{user._id}}" data-user-avatar="{{user.avatar}}" data-user-custom_username="{{user.custom_username}}" data-user-is_friend="{{user.is_friend}}" data-user-name="{{user.name}}">
		<div class="user-avatar"><img src="{{user.avatar}}" alt="" height="20" width="20"></div>
		<div class="user-status-circle"></div>
		<div class="user-title">{{user.name}}</div>
		<button class="btn btn-default" onclick="user_show_profile_by_id({{user._id}});">Show</button>
		<div class="clear"></div>
	</li>
</script>	
<script id="users-list-template-modal" type="text/x-handlebars-template">
	<li data-user-id="{{user._id}}" data-user-avatar="{{user.avatar}}" data-user-custom_username="{{user.custom_username}}" data-user-is_friend="{{user.is_friend}}" data-user-name="{{user.name}}">
		<div class="user-avatar"><img src="{{user.avatar}}" alt="" height="20" width="20"></div>
		<div class="user-status-circle"></div>
		<div class="user-title">{{user.name}}</div>
		<button class="btn btn-success" onclick="add_new_users_by_id({{user._id}});">Add</button>
		<div class="clear"></div>
	</li>
</script>	
<script id="user-profile-template" type="text/x-handlebars-template">
	<div class="panel panel-default user_profile {{#if is_online}}online{{else}}offline{{/if}}" data-user-id="{{user._id}}">
		<div class="panel-heading">
			<h3 class="panel-title">{{user.name}}</h3>
		</div>
		<div class="panel-body">
			<div class="user_profile-avatar thumbnail">
				<img src="{{user.avatar}}" height="200" width="200" >
			</div>
			<div class="user_profile-data">
				<b>{{user.name}}</b><br />
				username: {{user.custom_username}}<br />
				id: {{user._id}}<br />
				<button class="btn btn-success" onclick="make_the_call_to({{user._id}});">Call</button> 
				{{#if user.is_friend}}
					<button class="btn btn-error" onclick="user_friend_action('{{user._id}}','delete');">Remove as friend</button> 
				{{else}}
					<button class="btn btn-warning" onclick="user_friend_action('{{user._id}}','add');">Add as friend</button> 
				{{/if}}
			</div>
		</div>
	</div>
</script>	
<script id="chat-message-other-template" type="text/x-handlebars-template">
	<li class="type-other clearfix">
		<span class="chat-img pull-left">
			<span class="user-image user-image50" style="background-image: url('{{user.avatar}}'); "></span>
		</span>
		<div class="chat-body clearfix">
			<div class="header">
				<strong class="pull-right primary-font">{{user.name}}</strong> <span style="clear:both;"></span>
			</div>
			<p>
				{{text}}
			</p>
			<p>
				<time class="timeago toinit pull-left" datetime="{{timeiso8601}}"></time>
			</p>
		</div>
	</li>
</script>
<script id="chat-message-me-template" type="text/x-handlebars-template">
	<li class="type-me clearfix">
		<div class="chat-body clearfix">
			<p>
				{{text}}
			</p>
			<p>
				<time class="timeago toinit pull-left" datetime="{{timeiso8601}}"></time>
			</p>
		</div>
	</li>
</script>
<audio id="ringer" src="/static/sound/ringer.mp3" loop style="display:none;"></audio>
<!-- end handlebars templates -->