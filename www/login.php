<?php
	if(@$_POST['action'] == 'login') {
		$user = user::get(@$_POST['username']);
		if(!$user){
			$error = "Username not found. If you don't have an account enter in the app";
		} else if($user['password'] && $user['password'] != md5(@$_POST['password'])){
			$error = "Invalid password";
		} else {
			//e ok.
			$lses = session::create($user);
			if($lses){
				redirect();
			} else {
				$error = "Can't create session. Try again";
			}
		}
	} else if(@$_POST['action'] == 'register' || isset($_GET['auto_call_id'])){
		$user = new user();
		$user['time'] = time();
		$user['status'] = 'new';
		$user->save();
		
		$lses = session::create($user);
		redirect();
	}
	if(@$error){
		$error = '<div class="error">' . $error . '</div>';
	}
?>
<html>
	<head>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link href="/static/css/login.css" rel="stylesheet">
	</head>
	<body>
		<div class="container">
			<div id="login_register_box">
				<?php echo @$error; ?>
				<form action="" method="post" role="form">
					<input type="hidden" name="action" value="login" />
					<input type="text" name="username" value="<?php echo @$_POST['username']; ?>" placeholder="Username" class="form-control"  /><br/>
					<input type="password" name="password" placeholder="Password" class="form-control"  /><br />
					<input type="submit" value="Login" class="btn btn-primary" />
				</form>
				<br/>
				<div class="or">or</div>
				<br/>
				<form action="" method="post">
					<input type="hidden" name="action" value="register" />
					<input type="submit" value="Enter in the app" class="btn btn-warning" /><br />
					You can set your username later<br />or you can remain anonymous
				</form>
			</div>
		</div>
	</body>
</html>