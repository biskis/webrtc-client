<div class="right-side">
	<h3>
		Your id is: <b><?php echo $user['_id']; ?></b>
	</h3>
	<ul class="nav nav-pills">
		<li><a href="javascript:;" onclick="$('.panels-here .panel').hide(); $('.user_change_data').show();">Account settings</a></li>
		<li><a href="javascript:;" onclick="$('.panels-here .panel').hide(); $('.user_embed_code').show();">Embed code</a></li>
		<li><a href="javascript:;" onclick="logout()">Logout</a></li>
	</ul>
	<div class="panels-here">
		<div class="panel panel-default homepage">
			<div class="panel-heading">
				<h3 class="panel-title">Homepage</h3>
			</div>
			<div class="panel-body">
				Welcome to WebRTC!
			</div>
		</div>
		<div class="panel panel-default user_change_data">
			<div class="panel-heading">
				<h3 class="panel-title">Account settings</h3>
			</div>
			<div class="panel-body">
				<input type="text" class="form-control" id="user_change_username" placeholder="Choose a username" value="<?php echo $user['custom_username']; ?>" /><br/>
				<input type="password" class="form-control" id="user_change_password" placeholder="Select a password" /><br/>
				<input type="text" class="form-control" id="user_change_name" placeholder="Your name" value="<?php echo $user['name']; ?>" /><br/>
				<input type="text" class="form-control" id="user_change_avatar_url" placeholder="URL for your avatar" value="<?php echo $user['avatar']; ?>" /><br/>
				<input type="checkbox" id="user_change_s_auto_answer" <?php echo ($user->preference('auto_answer')) ? "checked" :""; ?> /> Auto answer<br/>
				<input type="checkbox" id="user_change_s_auto_save" <?php echo ($user->preference('auto_save')) ? "checked" :""; ?> /> Auto save files when receive<br/>
				<button class="btn btn-primary" onclick="return user_change_save();" >Save</button>
				<button class="btn btn-default" onclick="$('.user_change_data').hide();" >Cancel</button>
			</div>
		</div>
		<div class="panel panel-default user_embed_code" style="display:none;">
			<div class="panel-heading">
				<h3 class="panel-title">Embed code</h3>
			</div>
			<div class="panel-body">
				URL: <a href="<?php echo $www;?>?auto_call_id=<?php echo $user['_id']; ?>" target="_blank"><?php echo $www;?>?auto_call_id=<?php echo $user['_id']; ?></a><br /><br />
				Example: <br />
					<a href="<?php echo $www;?>?auto_call_id=<?php echo $user['_id']; ?>" target="_blank"><img src="<?php echo $www; ?>/static/img/btn_call_me_w.png" alt="Call me"></a>
					<br />Code: <br />
					<textarea cols="50"><a href="<?php echo $www;?>?auto_call_id=<?php echo $user['_id']; ?>" target="_blank"><img src="<?php echo $www; ?>/static/img/btn_call_me_w.png" alt="Call me"></a></textarea>
				<br /><br />
				Example: <br />
					<a href="<?php echo $www;?>?auto_call_id=<?php echo $user['_id']; ?>" target="_blank"><img src="<?php echo $www; ?>/static/img/btn_call_me_p.png" alt="Call me"></a>
					<br />Code: <br />
					<textarea cols="50"><a href="<?php echo $www;?>?auto_call_id=<?php echo $user['_id']; ?>" target="_blank"><img src="<?php echo $www; ?>/static/img/btn_call_me_p.png" alt="Call me"></a></textarea>
				
			</div>
		</div>
	</div>
</div>