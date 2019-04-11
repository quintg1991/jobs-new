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
  
  if($_SESSION['role'] != 1)
  {
    header('Location: dashboard.php');
  }

  if (isset($_POST['submit']))
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

    $_username = $_POST['username'];

    $_sql = "INSERT INTO `users` (`username`, `password`, `initials`, `role`, `added`) VALUES (:username, :password, :initials, :role, :added)";
    $_stmnt = $_pdo->prepare($_sql);

    $_params =
      array
      (
        ':username' => $_username,
        ':password' => sha1($_username . $_POST['password']),
        ':initials' => $_POST['initials'],
        ':role' => $_POST['role'],
        ':added' => date("Y-m-d H:i:s")
      );

    
    if($_stmnt->execute($_params))
    {
      echo "User created successfully";
      //echo $_pdo->lastInsertId();
    }
    else
    {
      echo $_stmnt->errorInfo();
    }
    
    //echo "created successfully";

  }

?>

<!doctype html>
<html>
  <head>
    <title>
      Administrator Page
    </title>

    <link rel="stylesheet" type="text/css" href="css/admin.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" />
    <!--link rel="stylesheet" href="https://www.una.edu/css/una.css" /-->


    <!-- Functions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script>
    </script>

  </head>
  <body>

    <?php
      include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/header.php");
    ?>

    <br />

    <div class="container-fluid">
      <div class="container">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <form name="addUser" method="POST" action="">

            <div class="form-group">
              <label class="control-label">
                Username
              </label>
              <input class="form-control" id="username" name="username" placeholder="Username" required="required" type="text"/>
            </div>

            <div class="form-group">
              <label class="control-label">
                Role
              </label>
              <select class="form-control" id="role" name="role">
                <option value="1">1</option>
                <option value="100">100</option>
                <option value="10000">10000</option>
              </select>
            </div>

            <div class="form-group">
              <label class="control-label">
                Initials
              </label>
              <input  class="form-control" id="initials" name="initials" placeholder="Initials" required="required" type="text"/>
            </div>

            <div class="form-group">
              <label class="control-label">
                Password
              </label>
              <input class="form-control" id="password" name="password" placeholder="Password" required="required" type="password"/>
            </div>

            <div class="form-group">
              <label class="control-label">
                Re-Enter Password
              </label>
              <input class="form-control" id="password" name="password" placeholder="Password" required="required" type="password" />
            </div>

            <div class="form-group">
              <button class="btn btn-purple" id="submit" name="submit" type="submit">
                Create User
              </button>            
            </div>

          </form>
        </div>
      </div>
    </div>
    
    <br />

    <?php
      include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/footer.php");
    ?>
  
  </body>
</html>