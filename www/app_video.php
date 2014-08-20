<div id="video_over_all" nr_video_remote="1">
	<div class="video_messages">
		<div class="chat_ext">
			<ul id="chat_messages" class="chat">
				
			</ul>
		</div>
		<div class="send_new_message">
			<input type="text" class="form-control" id="new_message" placeholder="New message" />
			<button class="btn btn-default" onclick="chat_send_new_message();">Send</button>
		</div>
	</div>
	<video id="video_me" muted autoplay></video>
	<video id="video_remote" autoplay class="video_remote"></video>
		
	<div class="btns">
		<button class="btn btn-default glyphicon glyphicon-comment" onclick="video_show_message_bar();"></button>
		<button class="btn btn-danger glyphicon glyphicon-earphone" onclick="hangup();"></button>
		<button class="btn btn-primary glyphicon glyphicon-upload" onclick="webrtc_send_file();"></button>
		<button class="btn btn-success glyphicon glyphicon-plus" onclick="add_new_users_click();"></button>
	</div>
</div>

