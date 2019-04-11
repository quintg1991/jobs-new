<?php

  session_start();

  /***                                          ***\

    File name: userManager.php
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


  //Select all parameters from database columns
  $_sql = "SELECT `users`.`id` AS `id`, `users`.`username` AS `username`, `users`.`initials` AS `initials`, `users`.`role` AS `role` FROM `users` ORDER BY `users`.`username` ASC";
  $_stmnt = $_pdo->prepare($_sql);

  //If successfully executed, fetch all column data as an associative array
  if($_stmnt->execute())
  {
    $_users = $_stmnt->fetchAll(PDO::FETCH_ASSOC);
  }
  else
  {
    $_users = array();
    //echo $_pdo->errorInfo();
  }

?>

<!doctype html>
<html>
  <head>
    <title>
      View Users
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
        <p>
          <a class="btn btn-success" href="user.php">
            <span class="glyphicon glyphicon-plus">
            </span>
            Add User
          </a>
        </p>
        <br />
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>
                  Username
                </th>
                <th>
                  Initials
                </th>
                <th>
                  Role
                </th>
                <th>
                  Action
                </th>
              </tr>
            </thead>
            <tbody>

              <?php
                if(count($_users) > 0)
                {
                  foreach($_users as $user)
                  {
                    echo '<tr>';
                    echo '<td>' . $user['username'] . '</td>';
                    echo '<td>' . $user['initials'] . '</td>';
                    echo '<td>' . $user['role'] . '</td>';
                    echo '<td>';
                    echo '<a class="btn btn-default" href="user.php?id=' . $user['id'] . '"><span class="glyphicon glyphicon-pencil"></a>';
                    echo '<a class="btn btn-default" href="delete.php?id=' . $user['id'] . '"><span class="glyphicon glyphicon-trash"></a>';
                    echo '</td>';
                    echo '</tr>';
                  }
                }
                else
                {
                  echo '<tr>';
                  echo '<td colspan="4">No users to display</td>';
                  echo '</tr>';
                }
              ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>

    <br />

    <?php
        include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/footer.php");
    ?>
  </body>
</html>
