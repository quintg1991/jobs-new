<?php

  session_start();

  /***                                          ***\

    File name: user.php
    Author: Quint GLover

    Copyright (c) 2017 University of North Alabama

  \***                                          ***/

  //default settings
  ini_set('magic_quotes_gpc', 'Off');
  date_default_timezone_set('America/Chicago');
  ini_set('display_errors', 'On');
  
  if($_SESSION['role'] != "admin")
  {
    header('Location: tasks.php');
  }

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

  $_id = (isset($_GET['id']) ? $_GET['id'] : '');

  $_sql = "SELECT `users`.`id` AS `id`, `users`.`username` AS `username`, `users`.`initials` AS `initials`, `users`.`role` AS `role`, `users`.`password` AS `password` FROM `users` WHERE `users`.`id`=:id";
  $_stmnt = $_pdo->prepare($_sql);

  $_params =
    array
    (
      ':id' => $_id
    );

  if($_stmnt->execute($_params))
  {
    $usr = $_stmnt->fetch(PDO::FETCH_ASSOC);
    //print_r($usr);
  }
  else
  {
    $usr = array();
    //echo "empty array";
  }

  if(isset($_POST['submit']))
  {
    if($_POST['id'] == 0)
    {    
      $_username = $_POST['username'];

      if($_POST['password'] == $_POST['vPassword'])
      {

        $_password = $_POST['password'];
      
        $_sql = "INSERT INTO `users` (`username`, `password`, `initials`, `role`, `added`) VALUES (:username, :password, :initials, :role, :added)";
        $_stmnt = $_pdo->prepare($_sql);

        $_params =
          array
          (
            ':username' => $_username,
            ':password' => sha1($_username . $_password),
            ':initials' => $_POST['initials'],
            ':role' => $_POST['role'],
            ':added' => date("Y-m-d H:i:s")
          );
        
        if($_stmnt->execute($_params))
        {
          header('Location: userManager.php');
          //echo $_pdo->lastInsertId();
        }
        else
        {
          // insert error...
          $err = 2;
        }
      }
      else
      {
        // passwords don't match
        $err = 1;
      }
    }
    else
    {
      if($_POST['password'] != '' && ($_POST['password'] === $_POST['vPassword']))
      {
        $_password = sha1($_POST['username'] . $_POST['password']);
      }
      else
      {
        $_password = $_POST['curPassword'];
      }

      if($_POST['initials'] == '')
      {
        $_initials = $usr['initials'];
      }
      else
      {
        $_initials = $_POST['initials'];
      }

      // update using form data
      $_sql = "UPDATE `users` SET `users`.`username`=:username, `users`.`role`=:role, `users`.`initials`=:initials, `users`.`password`=:password WHERE `users`.`id`=:id";
      $_stmnt = $_pdo->prepare($_sql);

      $_params = 
        array
        (
          ':username' => $_POST['username'],
          ':role' => $_POST['role'],
          ':initials' => $_initials,
          ':password' => $_password,
          ':id' => $_GET['id']
        );

      if($_stmnt->execute($_params))
      {
        //echo "inside the bottom if statment";
        header('location: userManager.php');
      }
      else
      {
        echo " update went wrong";
        // update went wrong...
        $err = 2;
      }
    }
  }
?>

<!doctype html>
<html>
  <head>
    <title>
      Administrator Page
    </title>

    <!--link rel="stylesheet" type="text/css" href="css/admin.css" /-->
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
            <input name="id" type="hidden" value="<?php echo ($_id != '' ? $_id : 0); ?>" />
            <input name="curPassword" type="hidden" value="<?php echo (count($usr) > 0 ? $usr['password'] : ''); ?>" />
            <div class="form-group">
              <label class="control-label">
                Username
              </label>
              <input class="form-control" id="username" name="username" placeholder="Username" required="required" type="text" value="<?php echo (count($usr) > 1 ? $usr['username'] : ''); ?>" <?php echo (count($usr) > 1 ? 'readonly="readonly"' : ''); ?> />
            </div>
            <div class="form-group">
              <label class="control-label">
                Role
              </label>
              <select class="form-control" id="role" name="role" <?php echo (count($usr) > 1 ? '' : 'required="required"'); ?>>
                <option value="">Select a Role</option>
                <option <?php echo ($usr['role'] == "admin" ? 'selected="selected"' : ''); ?> value="admin">admin</option>
                <option <?php echo ($usr['role'] == "user" ? 'selected="selected"' : ''); ?> value="user">user</option>
              </select>
            </div>
            <div class="form-group">
              <label class="control-label">
                Initials
              </label>
              <input  class="form-control" id="initials" name="initials" placeholder="Initials" <?php echo (count($usr) > 1 ? '' : 'required="required"'); ?> type="text"/>
            </div>
            <div class="form-group">
              <label class="control-label">
                Password
              </label>
              <input class="form-control" id="password" name="password" placeholder="Password" <?php echo (count($usr) > 1 ? '' : 'required="required"'); ?> type="password"/>
            </div>
            <div class="form-group">
              <label class="control-label">
                Re-Enter Password
              </label>
              <input class="form-control" id="vPassword" name="vPassword" placeholder="Password" <?php echo (count($usr) > 1 ? '' : 'required="required"'); ?> type="password" />
            </div>
            <div class="form-group">
              <button class="btn btn-purple" id="submit" name="submit" type="submit">
                Save
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