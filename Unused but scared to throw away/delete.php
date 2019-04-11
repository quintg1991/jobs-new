<?php

  session_start();

  /***                                          ***\

    File name: delete.php
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

  if(!isset($_GET['id']))
  {
    header('Location: userManager.php');
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

  $_sql = "DELETE FROM `users` WHERE `users`.`id`=:id";
  $_stmnt = $_pdo->prepare($_sql);

  $_params =
    array
    (
      ':id' => $_GET['id']
    );

  if($_stmnt->execute($_params))
  {
    header('Location: userManager.php');
  }
  else
  {
    header('Location: userManager.php?err=1');
    //echo $_pdo->errorInfo();
  }
