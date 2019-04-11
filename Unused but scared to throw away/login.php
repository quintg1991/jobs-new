<?php

  session_start();

  /***                                          ***\

    File name: config.ini
    Author: Quint GLover

    Copyright (c) 2017 University of North Alabama

  \***                                          ***/

  //default settings
	ini_set('magic_quotes_gpc', 'Off');
  date_default_timezone_set('America/Chicago');
  ini_set('display_errors', 'On');
	
  if(isset($_POST['submit']))
  {
    //read .ini file into array
    $_ini = parse_ini_file("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/etc/config.ini", true);

    //When connecting to the server, set names to utf8 encoding
    $_opt = array
    (
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
    ); 

    //If on localhost, create root PDO. Else, connect to the server with config.ini username and password.
    if($_SERVER['SERVER_NAME'] == 'localhost')
    {
      $_dsn = "{$_ini['database']['dialect']}:host=127.0.0.1;dbname=" . strtolower($_ini['database']['schema']);
    
      $_pdo = new PDO($_dsn, 'root', '', $_opt);
    }
    else
    {
      $_pdo = new PDO($_dsn, $_ini['database']['username'], $_ini['database']['password'], $_opt);
    }

    //Process the post data
    $_username = $_POST['username'];
    $_sql = "SELECT `users`.`id` AS `id`, `users`.`initials` AS `initials`, `users`.`role` AS `role` FROM `users` WHERE `users`.`username`=:username AND `users`.`password`=:password";
    $_stmnt = $_pdo->prepare($_sql);

    //Create an array $_params consisting of username and password
    $_params =
      array
      (
        ':username' => $_username,
        ':password' => sha1($_username . $_POST['password'])
      );

    //If able to execute, set up session parameters for the user. Else, terminate the session.
    if($_stmnt->execute($_params))
    {
      $_user = $_stmnt->fetch(PDO::FETCH_ASSOC);

      if($_user['id'] != '')
      {
        $_SESSION['user_id'] = $_user['id'];
        $_SESSION['role'] = $_user['role'];
        $_SESSION['initials'] = $_user['initials'];
        
        //session_write_close();
          header('Location: userManager.php');        
      }
      else
      {
        $err = 1;
      }
    }
    else
    {
      $err = $_stmnt->errorInfo();
      unset($_SESSION['user_id']);
      unset($_SESSION['role']);
      unset($_SESSION['initials']);
      session_unset();
      session_destroy();
    }

  }

?>

<!doctype html>
<html>
	<head>
		<title>
      Login Page
    </title>
    <link rel="stylesheet" type="text/css" href="css/login.css" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="https://www.una.edu/css/una.css" />


		<!-- Functions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script>
		</script>

	</head>
	<body>

		<!-- Header -->
		<div class="header" id="myHeader">
    </div>

		<br />

		<div class="login_container" id="sign-in">
			<div class="row">
				<form name="loginForm" method="POST" action="">

          <div class="form-group">
            <label class="control-label">
              Username
            </label>
            <input class="form-control" id="username" name="username" placeholder="Username" type="text" />
          </div>

          <div class="form-group">
            <label class="control-label">
              Password
            </label>
            <input class="form-control" id="password" name="password" placeholder="Password" type="password" />
          </div>

          <?php
            if(isset($err))
            {
          ?>

            <div class="alert alert-danger alert-dismissible" role="alert">
              <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">
                  &times;
                </span>
              </button>
              <strong>
                Error:
              </strong>
               Invalid username/password.
            </div>

          <?php
            }
          ?>

          <div class="form-group">
            <button class="btn btn-purple" id="submit" name="submit" type="submit">
              Login
            </button>
          </div>

				</form>
			</div>	
		</div>

		<br />

		<!-- Footer -->
		<div class="footer navbar-fixed-bottom" id="myFooter">
    </div>

	</body>

</html>