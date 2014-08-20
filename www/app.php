<html>
<head>
	<link rel="stylesheet" href="/static/css/bootstrap.min.css">
	
	<script src="/static/js/jquery-1.11.1.min.js"></script>
	<script src="/static/js/handlebars-v1.3.0.js"></script>
	<script src="/static/js/jquery.timeago.js"></script>
	<script src="/static/js/bootstrap.js" ></script>
	<script src="/static/js/noty/packaged/jquery.noty.packaged.min.js" ></script>
	
	<link href="/static/css/webrtc_app.css" rel="stylesheet">

	<script src="<?php echo $socketio_url; ?>/socket.io/socket.io.js"></script>
	<script src="/static/js/webrtc_adapter.js" ></script>
	<script>
		var socketio_url = "<?php echo $socketio_url; ?>";
		var my_username = "<?php echo $user['_id']; ?>";
		var me = <?php echo json_encode(session::get('user')->to_array()); ?>;
		var api_url = "<?php echo $api_url; ?>/json/";
		var friends = <?php echo json_encode($user->friends()); ?>;
		var settings = <?php echo json_encode($user->get_settings()); ?>;
		var auto_call_id = <?php echo (isset($_GET['auto_call_id'])) ? ("'".$_GET['auto_call_id']."'"): "false"; ?>;
	</script>
</head>
<body>
	<?php include("app_left.php"); ?>
	<?php include("app_right.php"); ?>
	<div class="clear"></div>

	<?php include("app_video.php"); ?>
	<?php include("app_extra.php"); ?>
	<script src="/static/js/extra_app.js" ></script>
	<script src="/static/js/webrtc_app.js" ></script>
</body>
</html>