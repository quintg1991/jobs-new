<?php
 session_start();

  /***                                          ***\

    File name: stats.php
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




  //============= User list query =====================================================
  $_usrLstSql = "SELECT `users`.`username` AS `username`, `users`.`initials` AS `initials`, `users`.`id` AS `id` FROM `users` ORDER BY `users`.`username`ASC";
  $_usrLstStmnt = $_pdo->prepare($_usrLstSql);

  if($_usrLstStmnt->execute())
  {
    $_usrLst = $_usrLstStmnt->fetchAll(PDO::FETCH_ASSOC);
  }
  else
  {
    $_usrLst = array();
  }
  //============= end user list query =================================================
?>

<!doctype html>
<html>
  <head>
    <title>
      Job Statistics
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

    <!--===== Header =====-->
    <?php
    include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/header.php")
    ?>

    <!--============ Body Content ==========-->
    <div class="container">
      <div class="row">
        <?php include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/sidebar.php")
        ?>
      </div>
    </div>

    <!--============ End Body Content ======-->

    <!--===== Footer =====-->
    <?php
    include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/footer.php")
    ?>

  </body>
</html>